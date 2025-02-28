<?php
function getProcessingData() {
    // Database connection
    include '../settings/config.php'; // Ensure your database connection details are included

    $query = "SELECT * FROM appointments WHERE status = 'Processing'";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        die("Database query failed: " . mysqli_error($conn));
    }

    $processing_data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $processing_data[] = $row; // Store each row in the array
    }

    return $processing_data; // Return the array of processing data
}
?>
