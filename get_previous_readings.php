<?php
include('config/db.php');

// Function to determine blood pressure category
function getBloodPressureCategory($systolic, $diastolic) {
    if ($systolic < 120 && $diastolic < 80) {
        return 'Normal';
    } elseif ($systolic >= 120 && $systolic <= 129 && $diastolic < 80) {
        return 'Elevated';
    } elseif (($systolic >= 130 && $systolic <= 139) || ($diastolic >= 80 && $diastolic <= 89)) {
        return 'Hypertension Stage 1';
    } elseif ($systolic >= 140 || $diastolic >= 90) {
        return 'Hypertension Stage 2';
    } elseif ($systolic > 180 || $diastolic > 120) {
        return 'Hypertensive Crisis';
    } else {
        return 'Normal'; // Default to Normal if no other condition is met
    }
}

// Fetch the patient's blood pressure records
if (isset($_GET['patient_id'])) {
    $patient_id = mysqli_real_escape_string($conn, $_GET['patient_id']);
    $query = "SELECT * FROM blood_pressure_records WHERE patient_id = '$patient_id' ORDER BY reading_time DESC";
    $result = mysqli_query($conn, $query);
    
    $readings = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $category = getBloodPressureCategory($row['systolic'], $row['diastolic']);
        $row['category'] = $category; // Add the category to the row data
        $readings[] = $row;
    }

    echo json_encode($readings);
}
?>
