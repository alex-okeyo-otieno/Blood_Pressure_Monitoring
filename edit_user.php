<?php
session_start();
include('config/db.php');

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Fetch user data to edit
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $query = "SELECT * FROM users WHERE id = $user_id";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);
}

// Handle form submission for updating user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $updateQuery = "UPDATE users SET username = '$username', role = '$role' WHERE id = $user_id";
    if (mysqli_query($conn, $updateQuery)) {
        $success = "User updated successfully!";
    } else {
        $error = "Error updating user!";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - BP Monitoring System</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 0 auto;
        }

        header {
            background-color: #3b4d6b;
            padding: 20px 0;
            color: white;
            text-align: center;
        }

        .navbar ul {
            display: flex;
            justify-content: flex-start;
            background-color: #4f6a8a;
            padding: 10px 0;
            margin: 0;
        }

        .navbar li {
            list-style: none;
            margin-right: 20px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            background-color: #5c78a5;
            border-radius: 5px;
        }

        .navbar a:hover {
            background-color: #4f6a8a;
        }

        main {
            margin-top: 30px;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            max-width: 500px;
            margin: 0 auto;
        }

        label {
            font-weight: bold;
            color: #555;
        }

        input, select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            color: #333;
        }

        input[type="password"] {
            font-family: 'Arial', sans-serif;
        }

        button {
            padding: 12px;
            background-color: #4f6a8a;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #3b4d6b;
        }

        .success, .error {
            text-align: center;
            padding: 10px;
            margin: 10px 0;
            font-weight: bold;
        }

        .success {
            color: green;
            background-color: #e0f7e0;
        }

        .error {
            color: red;
            background-color: #f7e0e0;
        }

        footer {
            text-align: center;
            padding: 15px;
            background-color: #3b4d6b;
            color: white;
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <div class="logo">
                <h1>BP Monitoring System</h1>
            </div>
        </header>

        <nav class="navbar">
            <ul>
                <li><a href="index.php" class="btn">Dashboard</a></li>
                <li><a href="manage_users.php" class="btn">Manage Users</a></li>
                <li><a href="logout.php" class="btn btn-danger">Logout</a></li>
            </ul>
        </nav>

        <main>
            <h2>Edit User</h2>

            <?php if (isset($success)) { echo "<div class='success'>$success</div>"; } ?>
            <?php if (isset($error)) { echo "<div class='error'>$error</div>"; } ?>

            <form method="POST" action="edit_user.php?id=<?php echo $user['id']; ?>">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" required><br>

                <label for="role">Role:</label>
                <select name="role" id="role" required>
                    <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                </select><br>

                <button type="submit" name="update_user">Update User</button>
            </form>
        </main>

        <footer>
            <p>&copy; <?php echo date("Y"); ?> BP Monitoring System | All Rights Reserved</p>
        </footer>
    </div>
</body>

</html>
