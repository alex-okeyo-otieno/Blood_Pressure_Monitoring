<?php
session_start();
include('config/db.php');

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Fetch all users from the database
$query = "SELECT * FROM users";
$result = mysqli_query($conn, $query);

// Handle adding a new user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $insertQuery = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
    if (mysqli_query($conn, $insertQuery)) {
        $success = "User added successfully!";
    } else {
        $error = "Error adding user!";
    }
}

// Handle deleting a user
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $deleteQuery = "DELETE FROM users WHERE id = $delete_id";
    if (mysqli_query($conn, $deleteQuery)) {
        $success = "User deleted successfully!";
    } else {
        $error = "Error deleting user!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - BP Monitoring System</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #4f6a8a;
            color: white;
        }

        table tr:hover {
            background-color: #f1f1f1;
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
            <h2>Manage Users</h2>

            <?php if (isset($success)) { echo "<div class='success'>$success</div>"; } ?>
            <?php if (isset($error)) { echo "<div class='error'>$error</div>"; } ?>

            <h3>Add New User</h3>
            <form method="POST" action="manage_users.php">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required><br>

                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required><br>

                <label for="role">Role:</label>
                <select name="role" id="role" required>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select><br>

                <button type="submit" name="add_user">Add User</button>
            </form>

            <h3>Existing Users</h3>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo ucfirst($user['role']); ?></td>
                            <td>
                                <a href="edit_user.php?id=<?php echo $user['id']; ?>">Edit</a> |
                                <a href="manage_users.php?delete_id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>

        <footer>
            <p>&copy; <?php echo date("Y"); ?> BP Monitoring System | All Rights Reserved</p>
        </footer>
    </div>
</body>

</html>
