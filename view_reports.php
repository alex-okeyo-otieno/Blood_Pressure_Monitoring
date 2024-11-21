<?php
session_start();
include('config/db.php');
require('fpdf/fpdf.php'); // Include FPDF library

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch user role
$user_role = $_SESSION['role'];

// Check if the report type is set
if (isset($_POST['report_type'])) {
    $report_type = $_POST['report_type'];

    // Generate the report based on the selected report type
    if ($report_type == 'all_patients') {
        generate_all_patients_report();
    } elseif ($report_type == 'individual_patient') {
        generate_individual_patient_report();
    } elseif ($report_type == 'trend_per_patient') {
        generate_trend_per_patient_report();
    }
} else {
    // Show the report selection form here
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Blood Pressure Reports</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <div class="container">
            <h2>Select Report Type</h2>
            <form method="POST" action="">
                <label for="report_type">Select Report Type:</label>
                <select name="report_type" id="report_type">
                    <option value="all_patients">All Patients Blood Pressure Readings</option>
                    <option value="individual_patient">Blood Pressure Breakdown per Patient</option>
                    <option value="trend_per_patient">Blood Pressure Trend per Patient</option>
                </select>
                <button type="submit">Proceed to Report Selection</button>
            </form>
        </div>
        <script src="script.js"></script>
    </body>
    </html>';
}

// Function to generate Blood Pressure Trend Report per Patient
function generate_trend_per_patient_report() {
    global $conn;

    // Fetch all patients
    $sql = "SELECT id, first_name, last_name FROM patients";
    $result = mysqli_query($conn, $sql);

    // Create PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(200, 10, 'Blood Pressure Trend per Patient', 0, 1, 'C');
    $pdf->Ln(10);

    // Table Header - Adjusted for better layout
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(45, 10, 'Patient Name', 1, 0, 'C');
    $pdf->Cell(35, 10, 'Systolic', 1, 0, 'C');
    $pdf->Cell(35, 10, 'Diastolic', 1, 0, 'C');
    $pdf->Cell(35, 10, 'Pulse Rate', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Reading Time', 1, 1, 'C');

    // Initialize patient count
    $total_patients = 0;

    // Loop through each patient and generate their trend report
    while ($row = mysqli_fetch_assoc($result)) {
        $patient_id = $row['id'];
        $patient_name = $row['first_name'] . ' ' . $row['last_name'];

        // Fetch blood pressure readings for this patient
        $bp_sql = "SELECT systolic, diastolic, pulse_rate, reading_time FROM blood_pressure_records WHERE patient_id = $patient_id ORDER BY reading_time";
        $bp_result = mysqli_query($conn, $bp_sql);

        // Display patient's name only once
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(45, 10, $patient_name, 1, 0, 'C');
        $pdf->Cell(35, 10, '', 1, 0, 'C'); // Empty cell for spacing
        $pdf->Cell(35, 10, '', 1, 0, 'C'); // Empty cell for spacing
        $pdf->Cell(35, 10, '', 1, 0, 'C'); // Empty cell for spacing
        $pdf->Cell(40, 10, '', 1, 1, 'C'); // Empty cell for spacing

        // Loop through each reading for this patient to show their trend
        while ($bp_row = mysqli_fetch_assoc($bp_result)) {
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(45, 10, '', 0, 0); // Empty cell for alignment
            $pdf->Cell(35, 10, $bp_row['systolic'], 1, 0, 'C');
            $pdf->Cell(35, 10, $bp_row['diastolic'], 1, 0, 'C');
            $pdf->Cell(35, 10, $bp_row['pulse_rate'], 1, 0, 'C');

            // Format the reading time to a shorter format
            $reading_time = date("Y-m-d H:i", strtotime($bp_row['reading_time']));
            $pdf->Cell(40, 10, $reading_time, 1, 1, 'C');
        }

        // Increment total patient count
        $total_patients++;
    }

    // Display the total number of patients
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(200, 10, "Total Patients: $total_patients", 0, 1, 'C');

    $pdf->Output('I', 'trend_per_patient_report.pdf');
}

// Function to generate All Patients Blood Pressure Report
function generate_all_patients_report() {
    global $conn;

    // Query to get all patient records and blood pressure readings
    $sql = "SELECT p.first_name, p.last_name, b.systolic, b.diastolic, b.pulse_rate, b.reading_time
            FROM blood_pressure_records b
            JOIN patients p ON b.patient_id = p.id
            ORDER BY b.reading_time";

    $result = mysqli_query($conn, $sql);

    // Create PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(200, 10, 'All Patients Blood Pressure Report', 0, 1, 'C');
    $pdf->Ln(10);

    // Table Header - Adjusted for better layout
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(45, 10, 'Patient Name', 1, 0, 'C');
    $pdf->Cell(35, 10, 'Systolic', 1, 0, 'C');
    $pdf->Cell(35, 10, 'Diastolic', 1, 0, 'C');
    $pdf->Cell(35, 10, 'Pulse Rate', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Reading Time', 1, 1, 'C');

    // Table Data
    $pdf->SetFont('Arial', '', 12);
    $last_patient_name = ''; // To track the patient and avoid repetition of name
    $printed_patients = [];  // Array to track printed patient names
    $patient_count = 0; // To keep track of the total number of unique patients

    while ($row = mysqli_fetch_assoc($result)) {
        // Concatenate first and last name to create full name
        $patient_name = $row['first_name'] . ' ' . $row['last_name'];

        // Check if patient name is already printed
        if (!in_array($patient_name, $printed_patients)) {
            // Display patient name only once per patient
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(45, 10, $patient_name, 1, 0, 'C');
            $pdf->Cell(35, 10, '', 1, 0, 'C'); // Empty cell for spacing
            $pdf->Cell(35, 10, '', 1, 0, 'C'); // Empty cell for spacing
            $pdf->Cell(35, 10, '', 1, 0, 'C'); // Empty cell for spacing
            $pdf->Cell(40, 10, '', 1, 1, 'C'); // Empty cell for spacing

            // Add this patient to the printed list and increment patient count
            $printed_patients[] = $patient_name;
            $patient_count++;
        }

        // Now, print the readings for this patient
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(45, 10, '', 0, 0); // Empty cell for alignment
        $pdf->Cell(35, 10, $row['systolic'], 1, 0, 'C');
        $pdf->Cell(35, 10, $row['diastolic'], 1, 0, 'C');
        $pdf->Cell(35, 10, $row['pulse_rate'], 1, 0, 'C');

        // Format the reading time
        $reading_time = date("Y-m-d H:i", strtotime($row['reading_time']));
        $pdf->Cell(40, 10, $reading_time, 1, 1, 'C');
    }

    // Display total number of unique patients
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(200, 10, "Total Patients: $patient_count", 0, 1, 'C');

    $pdf->Output('I', 'all_patients_report.pdf');
}

?>
