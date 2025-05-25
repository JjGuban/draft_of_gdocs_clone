<?php
session_start();
require_once '../core/dbConfig.php';
require_once '../core/models.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$docId = isset($_GET['doc_id']) ? intval($_GET['doc_id']) : 0;
$document = getDocumentById($docId);
$userId = $_SESSION['user_id'];

// Access check: only owner or shared users can access
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
    die("Access denied. You are not authorized to view this chat.");
}

$messages = getMessages($docId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Messages - <?= htmlspecialchars($document['title']) ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .chat-box {
            border: 1px solid #ccc;
            height: 300px;
            overflow-y: scroll;
            padding: 10px;
            background-color: #f9f9f9;
        }
        .chat-message {
            margin-bottom: 10px;
        }
        .chat-message strong {
            color: #333;
        }
        .chat-form {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="messages-container">
        <h2>Messages for: <?= htmlspecialchars($document['title']) ?></h2>
        <a href="index.php">← Back to Dashboard</a>

        <div class="chat-box" id="chatBox">
            <?php while ($msg = $messages->fetch_assoc()): ?>
                <div class="chat-message">
                    <strong><?= htmlspecialchars($msg['name']) ?>:</strong>
                    <?= htmlspecialchars($msg['message']) ?>
                    <small style="color:gray;">(<?= $msg['sent_at'] ?>)</small>
                </div>
            <?php endwhile; ?>
        </div>

        <form id="chatForm" class="chat-form">
            <input type="hidden" id="docId" value="<?= $docId ?>">
            <textarea id="message" placeholder="Type your message..." rows="3" required style="width: 100%;"></textarea><br>
            <button type="submit">Send</button>
        </form>
    </div>

    <script>
    $('#chatForm').submit(function(e) {
        e.preventDefault();

        const message = $('#message').val();
        const docId = $('#docId').val();

        if (message.trim() === '') return;

        $.post('../core/handleForms.php', {
            action: 'send_message',
            doc_id: docId,
            message: message
        }, function(res) {
            const result = JSON.parse(res);
            if (result.status === 'success') {
                location.reload(); // You can replace with auto-append for real-time effect
            } else {
                alert("Failed to send message.");
            }
        });
    });
    </script>
</body>
</html>
