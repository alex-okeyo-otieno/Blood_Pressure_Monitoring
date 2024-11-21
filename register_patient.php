<?php
include('config/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data and sanitize
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    // Insert patient data into database
    $query = "INSERT INTO patients (first_name, last_name, dob, gender, phone, address) 
              VALUES ('$first_name', '$last_name', '$dob', '$gender', '$phone', '$address')";
    
    if (mysqli_query($conn, $query)) {
        echo "<script type='text/javascript'>
                alert('Patient registered successfully!');
                window.location.href = 'capture_bp.php'; // Redirect to Capture Blood Pressure page
              </script>";
    } else {
        echo "<script type='text/javascript'>
                alert('Error: " . mysqli_error($conn) . "');
              </script>";
    }
}
?>

<!-- HTML Form with Inline CSS Styling -->
<form method="POST" style="max-width: 500px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); background-color: #f9f9f9;">
    <h2 style="text-align: center; color: #333;">Patient Registration</h2>
    <div style="margin-bottom: 15px;">
        <input type="text" name="first_name" placeholder="First Name" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px;">
    </div>
    <div style="margin-bottom: 15px;">
        <input type="text" name="last_name" placeholder="Last Name" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px;">
    </div>
    <div style="margin-bottom: 15px;">
        <input type="date" name="dob" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px;">
    </div>
    <div style="margin-bottom: 15px;">
        <select name="gender" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px;">
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
        </select>
    </div>
    <div style="margin-bottom: 15px;">
        <input type="text" name="phone" placeholder="Phone" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px;">
    </div>
    <div style="margin-bottom: 15px;">
        <textarea name="address" placeholder="Address" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px; height: 100px;"></textarea>
    </div>
    <div style="text-align: center;">
        <button type="submit" style="padding: 10px 20px; border: none; background-color: #28a745; color: white; font-size: 16px; border-radius: 4px; cursor: pointer;">Register</button>
    </div>
</form>

<!-- Navigation Button to Capture Blood Pressure -->
<div style="text-align: center; margin-top: 20px;">
    <a href="capture_bp.php" style="padding: 12px 24px; border: none; background-color: #007bff; color: white; font-size: 16px; border-radius: 4px; text-decoration: none; cursor: pointer;">
        Go to Capture Blood Pressure
    </a>
</div>
