<?php
session_start();
if (isset($_SESSION['user_id'])) {
    // Redirect logged-in users
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/index.php');
    } else {
        header('Location: users/index.php');
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Google Docs Clone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form id="loginForm">
            <input type="email" id="email" name="email" placeholder="Email" required><br>
            <input type="password" id="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
        <div id="loginMessage" style="color: red; margin-top: 10px;"></div>
    </div>

    <script>
    $('#loginForm').submit(function(e) {
        e.preventDefault();

        $.post('core/handleForms.php', {
            action: 'login',
            email: $('#email').val(),
            password: $('#password').val()
        }, function(response) {
            const res = JSON.parse(response);
            if (res.status === 'success') {
                window.location.reload(); // Auto-redirect based on role
            } else {
                $('#loginMessage').text(res.message);
            }
        });
    });
    </script>
</body>
</html>
