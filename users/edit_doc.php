<?php
session_start();
require_once '../core/dbConfig.php';
require_once '../core/models.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$docId = isset($_GET['doc_id']) ? intval($_GET['doc_id']) : 0;
$document = getDocumentById($docId);
$userId = $_SESSION['user_id'];

// Access check
if (!$document) {
    die("Document not found.");
}

// Check if user is owner or shared
$isOwner = $document['owner_id'] == $userId;
$sharedUsers = getSharedUsers($docId);
$isSharedUser = false;
foreach ($sharedUsers as $sharedUser) {
    if ($sharedUser['id'] == $userId) {
        $isSharedUser = true;
        break;
    }
}
if (!$isOwner && !$isSharedUser) {
    die("Access denied. You're not allowed to edit this document.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Document - <?= htmlspecialchars($document['title']) ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        #editor {
            width: 100%;
            min-height: 400px;
            border: 1px solid #ccc;
            padding: 15px;
            margin-top: 10px;
            background: #f9f9f9;
        }
        #status {
            font-size: 14px;
            color: green;
        }
    </style>
</head>
<body>
    <div class="editor-container">
        <h2>Editing: <?= htmlspecialchars($document['title']) ?></h2>
        <a href="index.php">← Back to Dashboard</a>

        <input type="hidden" id="docId" value="<?= $document['id'] ?>">
        <div id="editor" contenteditable="true"><?= $document['content'] ?></div>
        <div id="status"></div>
    </div>

    <script>
    let timeoutId;

    $('#editor').on('input', function () {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            const content = $('#editor').html();
            const docId = $('#docId').val();

            $.post('../core/handleForms.php', {
                action: 'autosave',
                doc_id: docId,
                content: content
            }, function (res) {
                const result = JSON.parse(res);
                if (result.status === 'success') {
                    $('#status').text('Auto-saved at ' + new Date().toLocaleTimeString());
                } else {
                    $('#status').text('Save failed');
                }
            });
        }, 1000); // 1 second after last keystroke
    });
    </script>
</body>
</html>
