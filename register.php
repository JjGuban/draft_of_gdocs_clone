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
    <title>Register - Google Docs Clone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- jQuery MUST be loaded before your custom script -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="register-container">
        <h2>Register</h2>
        <form id="registerForm">
            <input type="text" id="name" name="name" placeholder="Full Name" required><br>
            <input type="email" id="email" name="email" placeholder="Email" required><br>
            <input type="password" id="password" name="password" placeholder="Password" required><br>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="index.php">Login here</a></p>
        <div id="registerMessage" style="color: red; margin-top: 10px;"></div>
    </div>

    <!-- Inline script (safe and tested) -->
    <script>
    $(document).ready(function () {
        $('#registerForm').submit(function(e) {
            e.preventDefault();

            const name = $('#name').val().trim();
            const email = $('#email').val().trim();
            const password = $('#password').val();

            if (name === '' || email === '' || password === '') {
                $('#registerMessage').text('Please fill in all fields.');
                return;
            }

            $.post('core/handleForms.php', {
                action: 'register',
                name: name,
                email: email,
                password: password
            }, function(response) {
                try {
                    const res = JSON.parse(response);
                    if (res.status === 'success') {
                        alert("Registration successful. Redirecting to login...");
                        window.location.href = "index.php";
                    } else {
                        $('#registerMessage').text(res.message || "Registration failed.");
                    }
                } catch (err) {
                    $('#registerMessage').text("Unexpected server response.");
                    console.error("Invalid JSON response:", response);
                }
            });
        });
    });
    </script>
</body>
</html>
