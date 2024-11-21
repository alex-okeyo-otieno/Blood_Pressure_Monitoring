<?php
session_start();

include('config/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query to check if the user exists
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        // Check if password matches
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            header('Location: index.php'); // Redirect to dashboard after successful login
        } else {
            $error = "Invalid credentials!";
        }
    } else {
        $error = "Invalid credentials!";
    }
}
?>

<!-- HTML Form with Inline CSS Styling -->
<form method="POST" style="max-width: 400px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); background-color: #f9f9f9;">
    <h2 style="text-align: center; color: #333;">Login</h2>
    <div style="margin-bottom: 15px;">
        <input type="text" name="username" placeholder="Username" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px;">
    </div>
    <div style="margin-bottom: 15px;">
        <input type="password" id="password" name="password" placeholder="Password" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px;">
    </div>
    <div style="margin-bottom: 15px;">
        <label style="font-size: 14px;">
            <input type="checkbox" id="show-password" onclick="togglePassword()"> Show Password
        </label>
    </div>
    <div style="text-align: center;">
        <button type="submit" style="padding: 10px 20px; border: none; background-color: #007bff; color: white; font-size: 16px; border-radius: 4px; cursor: pointer;">Login</button>
    </div>
    <?php if (isset($error)) { echo "<p style='color: red; text-align: center;'>$error</p>"; } ?>
</form>

<!-- JavaScript to toggle password visibility -->
<script>
function togglePassword() {
    var passwordField = document.getElementById("password");
    var showPasswordCheckbox = document.getElementById("show-password");
    if (showPasswordCheckbox.checked) {
        passwordField.type = "text"; // Show the password
    } else {
        passwordField.type = "password"; // Hide the password
    }
}
</script>
