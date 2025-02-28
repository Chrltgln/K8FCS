<?php
include '../settings/authenticate.php';
checkUserRole(['Employee']); // Only allow users with the Employee role
include '../settings/config.php';

// Fetch total clients
$totalClientsQuery = "SELECT COUNT(*) as total_clients FROM appointments WHERE archived = 0";
$totalClientsResult = $conn->query($totalClientsQuery);
$totalClients = $totalClientsResult->fetch_assoc()['total_clients'];

// Fetch pending applications
$pendingApplicationsQuery = "SELECT COUNT(*) as pending_applications FROM appointments WHERE status = 'Processing' AND archived = 0";
$pendingApplicationsResult = $conn->query($pendingApplicationsQuery);
$pendingApplications = $pendingApplicationsResult->fetch_assoc()['pending_applications'];

// Fetch approved transactions
$approvedTransactionsQuery = "SELECT COUNT(*) as approved_transactions FROM appointments WHERE status = 'Approved' AND archived = 1";
$approvedTransactionsResult = $conn->query($approvedTransactionsQuery);
$approvedTransactions = $approvedTransactionsResult->fetch_assoc()['approved_transactions'];

// Fetch tasks for the logged-in user
$user_id = $_SESSION['user_id'];
$tasksQuery = "SELECT id, task FROM tasks WHERE user_id = ?";
$stmt = $conn->prepare($tasksQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$tasksResult = $stmt->get_result();
$tasks = [];
while ($row = $tasksResult->fetch_assoc()) {
    $tasks[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link rel="icon" href="../assets/images/updated-logo.webp" type="image/png">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <script src="../assets/js/dropdown.js" defer></script>
    <script src="assets/js/main.js" defer></script>
    <script src="assets/js/todolist.js" defer></script>
    <script src="../assets/js/hamburger.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/homepageemployee.css">
</head>

<body onload="clientList()">
    <?php include 'includes/navbar.php'; ?>

    <div class="dashboard">
        <header>
            <h2>Hello, <?php echo htmlspecialchars($_SESSION['clientName']); ?></h2>
        </header>
        <div class="stats">
            <div class="stat-item">
                <div class="stat-icon">👥</div>
                <div class="stat-info">
                    <p>Total Clients</p>
                    <h3><?php echo $totalClients; ?></h3>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon">✉️</div>
                <div class="stat-info">
                    <p>Pending Applications</p>
                    <h3><?php echo $pendingApplications; ?></h3>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon">✅</div>
                <div class="stat-info">
                    <p>Approved Transactions</p>
                    <h3><?php echo $approvedTransactions; ?></h3>
                </div>
            </div>
        </div>
        <div class="main-content">
            <div class="todo-list">
                <h3>To Do List</h3>
                <form action="processes/add-task.php" method="POST" class="task-form">
                    <input type="text" name="task" placeholder="Add a new task..." required>
                    <button type="submit">+</button>
                </form>
                <ul class="task-list">
                    <?php foreach ($tasks as $task): ?>
                        <li class="task-item">
                            <div class="task-item-text"><?php echo htmlspecialchars($task['task']); ?></div>
                            <form action="processes/delete-task.php" method="POST" class="delete-form">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                <button type="submit" class="delete-button">🗑</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="client-list">
                <h3>Appointment List</h3>
                <div id="client-list" class="client-list-content">
                    <?php
                    // Define and execute the SQL query
                    $sql = "SELECT clientname, email, status, appointment_date, appointment_time, form_type FROM appointments WHERE status = 'Processing' AND archived = 0";
                    $result = $conn->query($sql);

                    // Check if there are any results
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $statusClass = strtolower($row["status"]);
                            $formattedFormType = ucwords(str_replace('-', ' ', $row["form_type"]));
                            echo '<div class="client">';
                            echo '<div class="client-info">';
                            echo '<h3>' . htmlspecialchars($formattedFormType) . '</h3>';
                            echo '<p>Name: <strong>' . htmlspecialchars($row["clientname"]) . '</strong></p>';
                            echo '<p class="appointment-date">Appointment Date : ' . htmlspecialchars($row["appointment_date"]) . '</p>';
                            echo '<p class="appointment-time">Appointment Time : ' . htmlspecialchars($row["appointment_time"]) . '</p>';
                            echo '<p>Status: <span class="status ' . $statusClass . '">';
                            echo '<span class="status-dot ' . $statusClass . '"></span>';
                            echo htmlspecialchars($row["status"]) . '</span></p>';

                            echo '</div>';
                            echo '<div class="appointment-info">';

                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No appointments found.</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p style="text-align: center;">Copyright &copy; All rights reserved.</p>
    </footer>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script>
        function clientList() {
            const xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
                document.getElementById('client-list').innerHTML = this.responseText;
            }
            xhttp.open("GET", "fetch/fetch-pending-appointments.php", true);
            xhttp.send();
        }

        document.addEventListener("DOMContentLoaded", function() {
            clientList();
            setInterval(clientList, 1); 
        });
    </script>
</body>

</html>
<?php
$conn->close();
?>