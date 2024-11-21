<?php
session_start(); // Start the session to access $_SESSION variables

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('config/db.php');

// Function to get the last blood pressure reading
function getLastBloodPressure($conn, $patient_id) {
    $query = "SELECT * FROM blood_pressure_records WHERE patient_id = '$patient_id' ORDER BY reading_time DESC LIMIT 3";
    $result = mysqli_query($conn, $query);
    $readings = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $readings[] = $row;
    }
    return $readings;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = mysqli_real_escape_string($conn, $_POST['patient_id']);
    $systolic = mysqli_real_escape_string($conn, $_POST['systolic']);
    $diastolic = mysqli_real_escape_string($conn, $_POST['diastolic']);
    $pulse_rate = mysqli_real_escape_string($conn, $_POST['pulse_rate']); // Capture pulse rate
    $user_id = $_SESSION['user_id'];

    // Insert the blood pressure readings into the database
    $timestamp = date("Y-m-d H:i:s"); // Capture timestamp
    $query = "INSERT INTO blood_pressure_records (patient_id, systolic, diastolic, pulse_rate, captured_by, reading_time) 
              VALUES ('$patient_id', '$systolic', '$diastolic', '$pulse_rate', '$user_id', '$timestamp')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Blood pressure recorded successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

// Logout action
if (isset($_GET['logout'])) {
    session_destroy(); // Destroy the session to log out
    header("Location: login.php"); // Redirect to the dashboard
    exit();
}
?>

<!-- HTML Form -->
<form method="POST" style="max-width: 600px; margin: auto; padding: 20px; background-color: #f9f9f9; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
    <h2 style="text-align: center; color: #333; font-family: Arial, sans-serif;">Blood Pressure Capture</h2>

    <!-- Searchable Patient Dropdown -->
    <label for="patient_id" style="display: block; margin-bottom: 8px; font-size: 16px; color: #555;">Select Patient:</label>
    <input type="text" id="searchPatient" placeholder="Search for patient..." style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px; margin-bottom: 20px;" onkeyup="filterPatients()">
    
    <select name="patient_id" id="patientSelect" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px; margin-bottom: 20px;" onchange="updatePreviousReadings()">
        <option value="" disabled selected>Select a patient</option> <!-- Default empty option -->
        <?php
        $patients = mysqli_query($conn, "SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM patients");
        while ($patient = mysqli_fetch_assoc($patients)) {
            echo "<option value='{$patient['id']}'>{$patient['name']}</option>";
        }
        ?>
    </select>

    <div id="captureMessage" style="font-size: 16px; color: #555; margin-bottom: 20px; text-align: center;"></div>

    <label for="systolic" style="display: block; margin-bottom: 8px; font-size: 16px; color: #555;">Systolic (mmHg):</label>
    <input type="number" name="systolic" placeholder="Systolic" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px; margin-bottom: 20px;">

    <label for="diastolic" style="display: block; margin-bottom: 8px; font-size: 16px; color: #555;">Diastolic (mmHg):</label>
    <input type="number" name="diastolic" placeholder="Diastolic" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px; margin-bottom: 20px;">

    <label for="pulse_rate" style="display: block; margin-bottom: 8px; font-size: 16px; color: #555;">Pulse Rate (bpm):</label>
    <input type="number" name="pulse_rate" placeholder="Pulse Rate" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px; margin-bottom: 20px;">

    <button type="submit" style="width: 100%; padding: 12px; background-color: #007BFF; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">
        Capture BP
    </button>

    <!-- Buttons: Logout and Go Back to Dashboard -->
    <div style="display: flex; justify-content: space-between; margin-top: 20px;">
        <a href="?logout=true" style="padding: 12px; background-color: #dc3545; color: white; border-radius: 4px; font-size: 16px; cursor: pointer; text-align: center; width: 48%;">
            Logout
        </a>
        <a href="index.php" style="padding: 12px; background-color: #28a745; color: white; border-radius: 4px; font-size: 16px; cursor: pointer; text-align: center; width: 48%;">
            Go to Dashboard
        </a>
    </div>
</form>

<div id="previousReadings" style="margin-top: 30px;">
    <h3 style="font-size: 24px; color: #333; text-align: center; font-family: Arial, sans-serif;">Previous Readings</h3>
    
    <!-- Professional Table -->
    <table id="bpTable" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead style="background-color: #007BFF; color: white; text-align: left;">
            <tr>
                <th style="padding: 12px 15px; border: 1px solid #ddd; font-size: 16px; font-weight: bold;">#</th>
                <th style="padding: 12px 15px; border: 1px solid #ddd; font-size: 16px; font-weight: bold;">Systolic (mmHg)</th>
                <th style="padding: 12px 15px; border: 1px solid #ddd; font-size: 16px; font-weight: bold;">Diastolic (mmHg)</th>
                <th style="padding: 12px 15px; border: 1px solid #ddd; font-size: 16px; font-weight: bold;">Pulse Rate (bpm)</th> <!-- Added pulse rate -->
                <th style="padding: 12px 15px; border: 1px solid #ddd; font-size: 16px; font-weight: bold;">Captured At</th>
            </tr>
        </thead>
        <tbody id="bpTableBody">
            <!-- Dynamic Blood Pressure Records will appear here based on selected patient -->
        </tbody>
    </table>
</div>

<script>
    // Function to filter the patients based on the search input
    function filterPatients() {
        let input = document.getElementById('searchPatient').value.toLowerCase();
        let options = document.getElementById('patientSelect').options;
        let matched = false;

        // Clear the selected option
        document.getElementById('patientSelect').selectedIndex = 0;

        for (let i = 0; i < options.length; i++) {
            let option = options[i];
            let patientName = option.text.toLowerCase();
            
            // Show options that match the search input, hide others
            if (patientName.indexOf(input) > -1) {
                option.style.display = "block";
                matched = true; // Set matched to true if we find a match
            } else {
                option.style.display = "none";
            }
        }
    }

    // Update the previous readings when a patient is selected
   // Update the previous readings when a patient is selected
function updatePreviousReadings() {
    let patient_id = document.getElementById('patientSelect').value;
    if (!patient_id) return;

    // Fetch the last blood pressure records for the selected patient
    fetch(`get_previous_readings.php?patient_id=${patient_id}`)
        .then(response => response.json())
        .then(data => {
            let tableBody = document.getElementById('bpTableBody');
            tableBody.innerHTML = ''; // Clear previous records

            data.forEach((reading, index) => {
                let row = document.createElement('tr');
                
                // Set the color based on the blood pressure category
                let rowColor = '';
                switch (reading.category) {
                    case 'Normal':
                        rowColor = '#d4edda'; // Green for normal
                        break;
                    case 'Elevated':
                        rowColor = '#fff3cd'; // Yellow for elevated
                        break;
                    case 'Hypertension Stage 1':
                        rowColor = '#ffeeba'; // Light orange for stage 1
                        break;
                    case 'Hypertension Stage 2':
                        rowColor = '#f8d7da'; // Red for stage 2
                        break;
                    case 'Hypertensive Crisis':
                        rowColor = '#f1c2c2'; // Dark red for crisis
                        break;
                    default:
                        rowColor = '#ffffff'; // Default white
                }

                // Apply the color to the row
                row.style.backgroundColor = rowColor;

                // Add table data
                row.innerHTML = `
                    <td style="padding: 12px 15px; border: 1px solid #ddd; font-size: 16px;">${index + 1}</td>
                    <td style="padding: 12px 15px; border: 1px solid #ddd; font-size: 16px;">${reading.systolic}</td>
                    <td style="padding: 12px 15px; border: 1px solid #ddd; font-size: 16px;">${reading.diastolic}</td>
                    <td style="padding: 12px 15px; border: 1px solid #ddd; font-size: 16px;">${reading.pulse_rate}</td>
                    <td style="padding: 12px 15px; border: 1px solid #ddd; font-size: 16px;">${reading.reading_time}</td>
                `;
                tableBody.appendChild(row);
            });
        });
}

</script>

