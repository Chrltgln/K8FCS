<?php
ob_start(); // Start output buffering
require_once('../vendor/autoload.php'); // Ensure this line is included to autoload Composer dependencies
include '../settings/authenticate.php';
checkUserRole(['Employee']);
include '../settings/config.php';
require_once('processes/send-email-payment.php'); // Ensure this line is included to load the sendApprovalEmail function

function logActivity($conn, $user_email, $action, $file_name)
{
    $stmt = $conn->prepare("INSERT INTO activity_log (user_email, action, file_name) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user_email, $action, $file_name);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['amount'])) {
    // Process the form submission
    $amount = (float) $_POST['amount']; // Store amount directly in pesos
    $description = $_POST['description'];
    $remarks = $_POST['remarks'];
    $email = $_POST['email'];
    $clientname = $_POST['clientname'];
    $appointment_id = $_POST['appointment_id'];
    $status_type = $_POST['status_type'];
    $bank_partner = $_POST['bank_partner'];
    $term = $_POST['term'] ?? null;
    $amount_finance = $_POST['amount_finance'] ?? null;
    $maturity = $_POST['maturity'] ?? null;
    $check_release = $_POST['check_release'] ?? null;

    // Fetch transaction_id from the database
    $query = "SELECT transaction_id FROM appointments WHERE id='$appointment_id'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $transaction_id = $row['transaction_id'];

    // Update the appointment status in the database
    if ($status_type === 'Approved') {
        $new_status = 'Approved';
        $sql = "UPDATE appointments SET status = ?, approve_at = NOW(), archived = 0, bank_partner = ?, remarks = ?, amount = ?, term = ?, amount_finance = ?, maturity = ?, check_release = ?, payment_description = ? WHERE id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssisisssi", $new_status, $bank_partner, $remarks, $amount, $term, $amount_finance, $maturity, $check_release, $description, $appointment_id);

        if ($stmt->execute()) {
            // Insert the new status update into the status_updates table
            $sql = "INSERT INTO status_updates (appointment_id, status, status_type) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $appointment_id, $new_status, $status_type);
            $stmt->execute();

            // Log activity
            logActivity($conn, $_SESSION['user_email'], 'Approved Transaction ID: ' . $transaction_id, 'N/A');

            // Fetch client email and other details from the database
            $query = "SELECT email, transaction_id, form_type FROM appointments WHERE id='$appointment_id'";
            $result = $conn->query($query);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $client_email = $row['email'];
                $transaction_id = $row['transaction_id'];
                $form_type = $row['form_type'];

                // Generate the payment link
                $client = new \GuzzleHttp\Client();

                try {
                    $response = $client->request('POST', 'https://api.paymongo.com/v1/links', [
                        'body' => json_encode([
                            'data' => [
                                'attributes' => [
                                    'amount' => $amount * 100, // Convert to centavos for payment gateway
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
                        header("Location: accept-payment.php");
                        exit;
                    } else {
                        echo "<p>Approval email could not be sent. Please try again later.</p>";
                        echo "<p>Error: $approvalEmailResult</p>";
                        error_log("Failed to send approval email to $client_email with subject $subject and body $body");
                    }
                } catch (\GuzzleHttp\Exception\ClientException $e) {
                    echo "<p>Error: " . htmlspecialchars($e->getResponse()->getBody()->getContents()) . "</p>";
                } catch (\Exception $e) {
                    echo "<p>Unexpected error: " . htmlspecialchars($e->getMessage()) . "</p>";
                }
            } else {
                echo "No client email found for the given appointment ID.";
            }
        } else {
            echo "Error updating status: " . $conn->error;
        }

        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/paymentui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- script js for search -->
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <script src="assets/js/search.js" defer></script>
    <script src="../assets/js/dropdown.js" defer></script>
    <script src="../assets/js/hamburger.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="assets/js/main.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <style>
        /* ...existing styles... */
        .loading-spinner {
        display: none;
        position: fixed;
        z-index: 9999;
        top: 45%;
        left: 47%;
        transform: translate(-50%, -50%);
        border: 8px solid #f3f3f3;
        border-radius: 50%;
        border-top: 8px solid #3498db;
        width: 60px;
        height: 60px;
        animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9998;
        }

        /* Ensure the loader is centered on mobile devices */
        @media (max-width: 768px) {
        .loading-spinner {
            width: 40px;
            height: 40px;
            border-width: 6px;
            top: 45%; /* Move the spinner a bit up */
            left: 44%; /* Move the spinner a bit to the left */
        }-index: 9998;
        }
    </style>
</head>

<body>

    <?php
    include 'includes/navbar.php';
    require_once('../vendor/autoload.php');
    require_once('processes/send-email-payment.php');

    require_once('../settings/config.php');

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['amount'])) {
        // Display the form for additional input
        $appointment_id = $_POST['appointment_id'];
        $status_type = $_POST['status_type'];
        $email = $_POST['email'];
        $clientname = $_POST['clientname'];
        ?>
        <section class="paymentui">
            <div class="container">
                <h2>Set <?php echo htmlspecialchars($clientname); ?>'s Payment Details</h2>

                <form id="payment-form" action="payment-ui.php" method="post">
                    <input type="hidden" name="appointment_id" value="<?php echo htmlspecialchars($appointment_id); ?>">
                    <input type="hidden" name="status_type" value="<?php echo htmlspecialchars($status_type); ?>">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    <input type="hidden" name="clientname" value="<?php echo htmlspecialchars($clientname); ?>">

                    <div>
                        <label for="amount">Enter Amount (in pesos):</label>
                        <input type="number" id="amount" name="amount" placeholder="e.g., 1000">
                    </div>

                    <div>
                        <label for="description">Enter Description:</label>
                        <input type="text" id="description" name="description"
                            value="Chattel Mortgage Fees & Comprehensive Car Insurance">
                    </div>

                    <div>
                        <label for="remarks">Enter Remarks:</label>
                        <textarea id="remarks" name="remarks"
                            placeholder="Input specific amount of Chattel Mortgage Fees & Comprehensive Car Insurance"></textarea>
                    </div>

                    <div>
                        <label for="bank-partner">Select Bank Partner:</label>
                        <select id="bank-partner" name="bank_partner">
                            <option value=""></option>
                            <option value="JACCS">JACCS</option>
                            <option value="ORICO">ORICO</option>
                            <option value="Banco De Oro">Banco De Oro</option>
                            <option value="Security Bank">Security Bank</option>
                            <option value="MayBank">MayBank</option>
                        </select>
                    </div>

                    <?php if (in_array($status_type, ['brand-new', 'second-hand', 'sangla-orcr'])): ?>
                        <div>
                            <label for="term">Select Term:</label>
                            <select id="term" name="term">
                                <option value=""></option>
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
                        </div>

                        <div>
                            <label for="amount-finance">Enter Amount Finance:</label>
                            <input type="number" id="amount-finance" name="amount_finance" placeholder="e.g., 500000">
                        </div>

                        <div>
                            <label for="maturity">Enter Maturity:</label>
                            <input type="date" id="maturity" name="maturity">
                        </div>

                        <div>
                            <label for="check-release">Enter Check Release:</label>
                            <input type="date" id="check-release" name="check_release">
                        </div>
                    <?php endif; ?>

                    <button type="submit">Submit</button>

                </form>
            </div>
        </section>

        <?php
    } elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['appointment_id']) && isset($_GET['form_type'])) {
        // Display the form for additional input
        $appointment_id = $_GET['appointment_id'];
        $form_type = $_GET['form_type'];

        // Fetch client details from the database
        $query = "SELECT email, clientname FROM appointments WHERE id='$appointment_id'";
        $result = $conn->query($query);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $email = $row['email'];
            $clientname = $row['clientname'];
        } else {
            echo "No client details found for the given appointment ID.";
            exit;
        }
        ?>
        <section class="paymentui">
            <div class="container">
                <h2>Set <?php echo htmlspecialchars($clientname); ?>'s Payment Details</h2>
                <form id="payment-form" action="payment-ui.php" method="post">
                    <div class="payment-ui-container">
                        <input type="hidden" name="appointment_id" value="<?php echo htmlspecialchars($appointment_id); ?>">
                        <input type="hidden" name="status_type" value="Approved">
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                        <input type="hidden" name="clientname" value="<?php echo htmlspecialchars($clientname); ?>">

                        <div>
                            <label for="amount">Enter Amount:</label> <br>
                            <input type="number" id="amount" name="amount" placeholder="e.g., 1000">
                        </div>

                        <div>
                            <label for="description">Enter Description:</label> <br>
                            <input type="text" id="description" name="description"
                                value="Chattel Mortgage Fees & Comprehensive Car Insurance">
                        </div>

                        <div>
                            <label for="bank-partner">Select Bank Partner:</label> <br>
                            <select id="bank-partner" name="bank_partner">
                                <option value=""></option>
                                <option value="JACCS">JACCS</option>
                                <option value="ORICO">ORICO</option>
                                <option value="Banco De Oro">Banco De Oro</option>
                                <option value="Security Bank">Security Bank</option>
                                <option value="MayBank">MayBank</option>
                            </select>
                        </div>

                        <?php if (in_array($form_type, ['brand-new', 'second-hand', 'sangla-orcr'])): ?>
                            <div>
                                <label for="term">Select Term:</label> <br>
                                <select id="term" name="term">
                                    <option value=""></option>
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
                            </div>

                            <div>
                                <label for="amount-finance">Enter Amount Finance:</label> <br>
                                <input type="number" id="amount-finance" name="amount_finance" placeholder="e.g., 500000">
                            </div>

                            <div>
                                <label for="maturity">Enter Maturity:</label> <br>
                                <input type="date" id="maturity" name="maturity">
                            </div>

                            <div>
                                <label for="check-release">Enter Check Release:</label> <br>
                                <input type="date" id="check-release" name="check_release">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="remarks-container">
                        <label for="remarks">Enter Remarks:</label> <br>
                        <textarea id="remarks" name="remarks"
                            placeholder="Input specific amount of Chattel Mortgage Fees & Comprehensive Car Insurance"></textarea>
                    </div>

                    <div class="button-container">
                        <button type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </section>

        <?php
    }
    ?>
    <div class="loading-overlay"></div>
    <div class="loading-spinner"></div>
    <script>
        document.getElementById('payment-form').addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent the form from submitting immediately

            const amount = document.getElementById('amount').value;
            const description = document.getElementById('description').value;
            const remarks = document.getElementById('remarks').value;
            const bankPartner = document.getElementById('bank-partner').value;
            const term = document.getElementById('term') ? document.getElementById('term').value : null;
            const amountFinance = document.getElementById('amount-finance') ? document.getElementById('amount-finance').value : null;
            const maturity = document.getElementById('maturity') ? document.getElementById('maturity').value : null;
            const checkRelease = document.getElementById('check-release') ? document.getElementById('check-release').value : null;
            const today = new Date().toISOString().split('T')[0];

            if (!amount || amount < 100) {
                Swal.fire({
                    title: 'Invalid Field',
                    text: 'Please enter an amount of 100 pesos or above.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            if (!description) {
                Swal.fire({
                    title: 'Invalid Field',
                    text: 'Please enter a description.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            if (!bankPartner) {
                Swal.fire({
                    title: 'Invalid Field',
                    text: 'Please select a bank partner.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            if (term === null || term === '') {
                Swal.fire({
                    title: 'Invalid Field',
                    text: 'Please select a term.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            if (amountFinance === null || amountFinance === '' || amountFinance < 200000) {
                Swal.fire({
                    title: 'Invalid Field',
                    text: 'Please enter an amount finance of 200000 pesos or above.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            if (maturity === null || maturity === '') {
                Swal.fire({
                    title: 'Invalid Field',
                    text: 'Please enter the maturity date.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            const maturityDate = new Date(maturity);
            if (maturity < today) {
                Swal.fire({
                    title: 'Invalid Date',
                    text: 'Maturity date cannot be in the past.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return false;
            }
            if (maturityDate.getDay() === 0 || maturityDate.getDay() === 6) {
                Swal.fire({
                    title: 'Invalid Date',
                    text: 'Maturity date cannot be on a weekend.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            if (checkRelease === null || checkRelease === '') {
                Swal.fire({
                    title: 'Invalid Field',
                    text: 'Please enter the check release date.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            const checkReleaseDate = new Date(checkRelease);
            if (checkRelease < today) {
                Swal.fire({
                    title: 'Invalid Date',
                    text: 'Check release date cannot be in the past.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return false;
            }
            if (checkReleaseDate.getDay() === 0 || checkReleaseDate.getDay() === 6) {
                Swal.fire({
                    title: 'Invalid Date',
                    text: 'Check release date cannot be on a weekend.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            if (!remarks) {
                Swal.fire({
                    title: 'Invalid Field',
                    text: 'Please enter your remarks.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to submit the payment details?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#218838',
                cancelButtonColor: '#585a5e',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.querySelector('.loading-overlay').style.display = 'block';
                    document.querySelector('.loading-spinner').style.display = 'block';
                    fetch('payment-ui.php', {
                        method: 'POST',
                        body: new FormData(this)
                    })
                    .then(response => response.text())
                    .then(data => {
                        document.querySelector('.loading-overlay').style.display = 'none';
                        document.querySelector('.loading-spinner').style.display = 'none';
                        Swal.fire({
                            title: 'Success!',
                            text: 'Payment details submitted successfully.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = 'accept-payment.php';
                        });
                    })
                    .catch(error => {
                        document.querySelector('.loading-overlay').style.display = 'none';
                        document.querySelector('.loading-spinner').style.display = 'none';
                        Swal.fire({
                            title: 'Error!',
                            text: 'There was an error submitting the payment details.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
                }
            });
        });
    </script>
</body>

</html>
<?php
ob_end_flush(); // End output buffering and flush the output
?>