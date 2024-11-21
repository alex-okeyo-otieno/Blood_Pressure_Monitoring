<?php
include('config/db.php'); // Ensure this points to your database connection file

// Password hashing
$password = password_hash('adminpassword', PASSWORD_DEFAULT);

// Insert admin user
$query = "INSERT INTO users (username, password, role) VALUES ('admin', '$password', 'admin')";
if (mysqli_query($conn, $query)) {
    echo "Admin user created successfully.";
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
