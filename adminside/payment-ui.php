<?php
// Check if the user is logged in and has the admin role
include '../settings/authenticate.php';
checkUserRole(['Admin']);
include '../settings/config.php';

function logActivity($conn, $user_email, $action, $file_name) {
    $stmt = $conn->prepare("INSERT INTO activity_log (user_email, action, file_name) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user_email, $action, $file_name);
    $stmt->execute();
    $stmt->close();
}

// Start output buffering
ob_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="grid-container">
        <!-- Header -->
        <?php include 'required/header.php'; ?>
        <!-- Sidebar -->
        <?php include 'required/sidebar.php'; ?>

        <!-- Main -->
        <main class="main-container">
            <div class="main-title">
                <?php if (isset($_POST['status_type']) && $_POST['status_type'] === 'Declined') { ?>
                    <h2>Are you sure you want to decline <?php echo htmlspecialchars($_POST['clientname']); ?>?</h2>
                <?php } else { ?>
                    <h2>Payment Section</h2>
                <?php } ?>
            </div>

            <?php
            require_once('../vendor/autoload.php');
            require_once('processes/send-email-payment.php');
            require_once('processes/send-email-decline.php');

            // Define the sendDeclineEmail function
            
            require_once('../settings/config.php');

            if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['amount']) && !isset($_POST['remarksForDecline']) && !isset($_POST['remarksForApproved'])) {
                // Display the form for additional input
                $appointment_id = $_POST['appointment_id'];
                $status_type = $_POST['status_type'];
                $email = $_POST['email'];
                $clientname = $_POST['clientname'];
                $bank_partner = isset($_POST['bank_partner']) ? $_POST['bank_partner'] : '';
                $remarks = $_POST['remarks'];
                $form_type = $_POST['form_type'];
                ?>

                <section class="paymentui">
                    <?php if ($status_type === 'Approved' && ($form_type === 'brand-new' || $form_type === 'second-hand' || $form_type === 'sangla-orcr')) { ?>
                        <h2>Set <?php echo htmlspecialchars($clientname); ?>'s Payment Details</h2>
                        <form action="payment-ui.php" method="post">
                            <input type="hidden" name="appointment_id" value="<?php echo htmlspecialchars($appointment_id); ?>">
                            <input type="hidden" name="status_type" value="<?php echo htmlspecialchars($status_type); ?>">
                            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                            <input type="hidden" name="clientname" value="<?php echo htmlspecialchars($clientname); ?>">
                            <input type="hidden" name="remarks" value="<?php echo htmlspecialchars($remarks); ?>">
                            <input type="hidden" name="form_type" value="<?php echo htmlspecialchars($form_type); ?>">
                            <input type="hidden" name="remarksForApproved" value="<?php echo htmlspecialchars($remarks); ?>">

                            <label for="bank_partner">Select Bank Partner:</label>
                            <select id="bank_partner" name="bank_partner" required>
                                <option value="">--Select Bank Partner--</option>
                                <option value="JACCS">JACCS</option>
                                <option value="ORICO">ORICO</option>
                                <option value="Banco De Oro">Banco De Oro</option>
                                <option value="Security Bank">Security Bank</option>
                                <option value="MayBank">MayBank</option>
                                <!-- Add more bank options as needed -->
                            </select>

                            <?php if ($form_type !== 'sangla-orcr') { ?>
                                
                                <label for="amount">Enter Amount (in pesos):</label>
                                <input type="number" id="amount" name="amount" step="0.01" required placeholder="e.g., 1000.00">

                                <label for="description">Enter Description:</label>
                                <input type="text" id="description" name="description"
                                    value="Chattel Mortgage Fees & Comprehensive Car Insurance">
                            <?php } ?>

                            <label for="remarks">Enter Remarks:</label>
                            <textarea id="remarks" name="remarks" required
                            placeholder="<?php echo ($form_type === 'sangla-orcr') ? 'Enter your remarks here...' : 'Input specific amount of Chattel Mortgage Fees & Comprehensive Car Insurance'; ?>"></textarea>
                            
                            <label for="term">Select Term:</label>
                            <select id="term" name="term" required>
                                <option value="6">6 months</option>
                                <option value="12">12 months</option>
                                <option value="18">18 months</option>
                                <option value="24">24 months</option>
                                <option value="30">30 months</option>
                                <option value="36">36 months</option>
                                <option value="42">42 months</option>
                                <option value="48">48 months</option>
                                <option value="54">54 months</option>
                                <option value="60">60 months</option>
                            </select>

                            <label for="amount-finance">Enter Amount Finance:</label>
                            <input type="number" id="amount-finance" name="amount_finance" required placeholder="e.g., 500000">

                            <label for="maturity">Enter Maturity:</label>
                            <input type="date" id="maturity" name="maturity" required>

                            <label for="check-release">Enter Check Release:</label>
                            <input type="date" id="check-release" name="check_release" required>

                            <button type="submit">Submit</button>
                        </form>
                    <?php } else if ($status_type === 'Declined') { ?>
                        <h2>Please specify the reason why you need to decline.</h2>
                        <form action="payment-ui.php" method="post">
                            <input type="hidden" name="appointment_id" value="<?php echo htmlspecialchars($appointment_id); ?>">
                            <input type="hidden" name="status_type" value="<?php echo htmlspecialchars($status_type); ?>">
                            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                            <input type="hidden" name="clientname" value="<?php echo htmlspecialchars($clientname); ?>">
                            <input type="hidden" name="bank_partner" value="<?php echo htmlspecialchars($bank_partner); ?>">
                            <input type="hidden" name="remarks" value="<?php echo htmlspecialchars($remarks); ?>">
                            <input type="hidden" name="form_type" value="<?php echo htmlspecialchars($form_type); ?>">

                            <label for="bank_partner">Select Bank Partner:</label>
                            <select id="bank_partner" name="bank_partner" required>
                                <option value="">--Select Bank Partner--</option>
                                <option value="JACCS">JACCS</option>
                                <option value="ORICO">ORICO</option>
                                <option value="Banco De Oro">Banco De Oro</option>
                                <option value="Security Bank">Security Bank</option>
                                <option value="MayBank">MayBank</option>
                                <!-- Add more bank options as needed -->
                            </select>
                            <textarea id="remarksForDecline" name="remarksForDecline" required
                                placeholder="Ex. Cancelled by Client"></textarea>
                            
                            <button type="submit">Submit</button>
                        </form>
                    <?php } ?>
                </section>

                <?php
            } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && (isset($_POST['amount']) || isset($_POST['remarksForDecline']) || isset($_POST['remarksForApproved']))) {
                // Process the form submission for approval or decline
                $amount = isset($_POST['amount']) ? (float) $_POST['amount'] : null;
                $description = isset($_POST['description']) ? $_POST['description'] : null;
                $remarks = isset($_POST['remarks']) ? $_POST['remarks'] : null;
                $remarksForDecline = isset($_POST['remarksForDecline']) ? $_POST['remarksForDecline'] : null;
                $remarksForApproved = isset($_POST['remarksForApproved']) ? $_POST['remarksForApproved'] : null;
                $email = $_POST['email'];
                $clientname = $_POST['clientname'];
                $appointment_id = $_POST['appointment_id'];
                $status_type = $_POST['status_type'];
                $bank_partner = $_POST['bank_partner'];
                $form_type = $_POST['form_type'];
                $term = $_POST['term'] ?? null;
                $amount_finance = $_POST['amount_finance'] ?? null;
                $maturity = $_POST['maturity'] ?? null;
                $check_release = $_POST['check_release'] ?? null;

                // Update the appointment status in the database
                if ($status_type === 'Approved' && ($form_type === 'brand-new' || $form_type === 'second-hand')) {
                    $new_status = 'Approved';
                    $sql = "UPDATE appointments SET status = ?, approve_at = NOW(), archived = 0, bank_partner = ?, remarks = ?, amount = ?, term = ?, amount_finance = ?, maturity = ?, check_release = ?, payment_description = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssisisssi", $new_status, $bank_partner, $remarks, $amount, $term, $amount_finance, $maturity, $check_release, $description, $appointment_id);
                } elseif ($status_type === 'Approved' && $form_type === 'sangla-orcr') {
                    $new_status = 'Approved';
                    $sql = "UPDATE appointments SET status = ?, approve_at = NOW(), archived = 1, paid = 2, remarks = ?, bank_partner = ?, term = ?, amount_finance = ?, maturity = ?, check_release = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssssissi", $new_status, $remarks, $bank_partner, $term, $amount_finance, $maturity, $check_release, $appointment_id);
                } elseif ($status_type === 'Declined') {
                    $new_status = 'Declined';
                    $sql = "UPDATE appointments SET status = ?, decline_at = NOW(), archived = 1, remarks = ?, bank_partner = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssi", $new_status, $remarksForDecline, $bank_partner, $appointment_id);
                } else {
                    echo "Error: Invalid status type.";
                    exit;
                }

                if ($stmt->execute()) {
                    // Insert the new status update into the status_updates table
                    $sql = "INSERT INTO status_updates (appointment_id, status, status_type) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iss", $appointment_id, $new_status, $status_type);
                    $stmt->execute();

                    // Fetch client email and other details from the database
                    $query = "SELECT email, transaction_id, form_type FROM appointments WHERE id='$appointment_id'";
                    $result = $conn->query($query);
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $client_email = $row['email'];
                        $transaction_id = $row['transaction_id'];
                        $form_type = $row['form_type'];

                        if ($status_type === 'Approved' && $form_type !== 'sangla-orcr') {
                            // Generate the payment link
                            $client = new \GuzzleHttp\Client();

                            try {
                                $response = $client->request('POST', 'https://api.paymongo.com/v1/links', [
                                    'body' => json_encode([
                                        'data' => [
                                            'attributes' => [
                                                'amount' => $amount * 100, // Convert to centavos for PayMongo
                                                'description' => $description,
                                                'remarks' => $remarks
                                            ]
                                        ]
                                    ]),
                                    'headers' => [
                                        'accept' => 'application/json',
                                        'authorization' => 'Basic ' . base64_encode('sk_test_sMcer2MgvoctE3UBoCUrgFkx'),
                                        'content-type' => 'application/json',
                                    ],
                                ]);

                                $responseBody = json_decode($response->getBody(), true);
                                $checkoutUrl = $responseBody['data']['attributes']['checkout_url'];

                                // Send combined email using the updated sendApprovalEmail function
                                $approvalEmailResult = sendApprovalEmail($client_email, $clientname, $form_type, $transaction_id, $checkoutUrl, $amount_finance, $term, $maturity, $check_release, $description);
                                if ($approvalEmailResult === true) {
                                    // Log activity
                                    logActivity($conn, $_SESSION['user_email'], 'Approved appointment w/ Transaction ID: ' . $transaction_id, 'payment-ui.php');
                                    echo "<script>
                                        Swal.fire({
                                            title: 'Successfully Approved',
                                            text: 'email sent to $client_email',
                                            icon: 'success',
                                            confirmButtonText: 'OK'
                                        }).then(() => {
                                            window.location.href = 'approve-payment.php';
                                        });
                                    </script>";
                                } else {
                                    echo "<p>Approval email could not be sent. Please try again later.</p>";
                                    echo "<p>Error: $approvalEmailResult</p>";
                                    error_log("Failed to send approval email to $client_email");
                                }
                            } catch (\GuzzleHttp\Exception\ClientException $e) {
                                echo "<p>Error: " . htmlspecialchars($e->getResponse()->getBody()->getContents()) . "</p>";
                            } catch (\Exception $e) {
                                echo "<p>Unexpected error: " . htmlspecialchars($e->getMessage()) . "</p>";
                            }
                        } elseif ($status_type === 'Approved' && $form_type === 'sangla-orcr') {
                            // Send approval email using the sendApprovalEmail function
                            $approvalEmailResult = sendApprovalEmailforSangalaOrcr($client_email, $clientname, $form_type, $transaction_id,$remarks);
                            if ($approvalEmailResult === true) {
                                // Log activity
                                logActivity($conn, $_SESSION['user_email'], 'Approved appointment w/ Transaction ID: ' . $transaction_id, 'payment-ui.php');
                                echo "<script>
                                    Swal.fire({
                                        title: 'Success!',
                                        text: 'Successfully approved transaction ID: $transaction_id, with remarks: $remarks email sent to $client_email',
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        window.location.href = 'archives.php';
                                    });
                                </script>";
                            } else {
                                echo "<p>Approval email could not be sent. Please try again later.</p>";
                                echo "<p>Error: $approvalEmailResult</p>";
                                error_log("Failed to send approval email to $client_email");
                            }
                        } else {
                            // Send decline email using the sendDeclineEmail function
                            $declineEmailResult = sendDeclineEmail($client_email, $clientname, $form_type, $transaction_id, $remarksForDecline);
                            if ($declineEmailResult === true) {
                                // Log activity
                                logActivity($conn, $_SESSION['user_email'], 'Declined appointment w/ Transaction ID: ' . $transaction_id, 'payment-ui.php');
                                echo "<script>
                                    Swal.fire({
                                        title: 'Success!',
                                        text: 'Successfully declined transaction ID: $transaction_id, with remarks: $remarksForDecline, and email sent to $client_email',
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        window.location.href = 'archives.php';
                                    });
                                </script>";
                            } else {
                                echo "<p>Decline email could not be sent. Please try again later.</p>";
                                echo "<p>Error: $declineEmailResult</p>";
                                error_log("Failed to send decline email to $client_email");
                            }
                        }
                    } else {
                        echo "No client email found for the given appointment ID.";
                    }
                } else {
                    echo "Error updating status: " . $conn->error;
                }

                $stmt->close();
                $conn->close();
                exit();
            }
            ?>
        </main>
        <!-- End Main -->
    </div>

    <script src="../assets/js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var today = new Date().toISOString().split('T')[0];
            document.getElementById('maturity').setAttribute('min', today);
            document.getElementById('check-release').setAttribute('min', today);
        });
    </script>
</body>

</html>

<?php
// End output buffering and flush the output
ob_end_flush();
?>

