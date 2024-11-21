<?php
session_start();
include('config/db.php');

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch user role
$user_role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - BP Monitoring System</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <h1>BP Monitoring System</h1>
            </div>
            <div class="greeting">
                <p>Welcome, <span><?php echo ucfirst($user_role); ?></span>!</p>
            </div>
        </header>

        <nav class="navbar">
            <ul>
                <?php if ($user_role === 'admin'): ?>
                    <li><a href="register_patient.php" class="btn">Register Patient</a></li>
                    <li><a href="capture_bp.php" class="btn">Capture Blood Pressure</a></li>
                    <li><a href="view_reports.php" class="btn">View Reports</a></li>
                    <li><a href="manage_users.php" class="btn">Manage Users</a></li>
                <?php elseif ($user_role === 'user'): ?>
                    <li><a href="capture_bp.php" class="btn">Capture Blood Pressure</a></li>
                <?php endif; ?>
                <li><a href="logout.php" class="btn btn-danger">Logout</a></li>
            </ul>
        </nav>

        <footer class="footer">
            <p>&copy; <?php echo date("Y"); ?> BP Monitoring System | All Rights Reserved</p>
        </footer>
    </div>

    <script src="script.js"></script>
</body>

</html>
