<?php
session_start();
require_once '../core/dbConfig.php';
require_once '../core/models.php';

// Redirect if not logged in or not a user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Document - Google Docs Clone</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="create-doc-container">
        <h2>Create a New Document</h2>
        <form id="createDocForm">
            <input type="text" id="title" name="title" placeholder="Document Title" required><br>
            <textarea id="content" name="content" placeholder="Write something..." rows="10" cols="50" required></textarea><br>
            <button type="submit">Create Document</button>
        </form>
        <div id="createMessage" style="margin-top: 10px; color: red;"></div>
    </div>

    <script>
    $('#createDocForm').submit(function(e) {
        e.preventDefault();

        $.post('../core/handleForms.php', {
            action: 'create_document',
            title: $('#title').val(),
            content: $('#content').val()
        }, function(response) {
            const res = JSON.parse(response);
            if (res.status === 'success') {
                alert('Document created successfully!');
                window.location.href = 'index.php'; // or optionally redirect to the new editor
            } else {
                $('#createMessage').text(res.message || "Failed to create document.");
            }
        });
    });
    </script>
</body>
</html>
