<?php
session_start();
require_once '../core/dbConfig.php';
require_once '../core/models.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../index.php');
    exit;
}

$docId = isset($_GET['doc_id']) ? intval($_GET['doc_id']) : 0;
$document = getDocumentById($docId);

// Check if document exists and is owned by the user
if (!$document || $document['owner_id'] != $_SESSION['user_id']) {
    die("Access denied or document not found.");
}

$sharedUsers = getSharedUsers($docId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Share Document - <?= htmlspecialchars($document['title']) ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        #searchResults {
            border: 1px solid #ccc;
            margin-top: 5px;
            max-height: 200px;
            overflow-y: auto;
        }
        .user-item {
            padding: 8px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }
        .user-item:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="share-container">
        <h2>Share "<?= htmlspecialchars($document['title']) ?>"</h2>
        <a href="index.php">← Back to Dashboard</a>
        <br><br>

        <input type="hidden" id="docId" value="<?= $docId ?>">

        <label for="userSearch">Search User by Name or Email:</label><br>
        <input type="text" id="userSearch" autocomplete="off" placeholder="Start typing...">
        <div id="searchResults"></div>

        <h3>Already Shared With:</h3>
        <ul>
            <?php while ($user = $sharedUsers->fetch_assoc()): ?>
                <li><?= htmlspecialchars($user['name']) ?> (<?= $user['email'] ?>)</li>
            <?php endwhile; ?>
        </ul>
    </div>

    <script>
    $('#userSearch').on('input', function() {
        const term = $(this).val();
        if (term.length < 2) {
            $('#searchResults').html('');
            return;
        }

        $.get('../core/handleForms.php', {
            action: 'search_user',
            term: term
        }, function(response) {
            const users = JSON.parse(response);
            let html = '';
            users.forEach(user => {
                html += `<div class="user-item" data-id="${user.id}">
                            ${user.name} (${user.email})
                        </div>`;
            });
            $('#searchResults').html(html);
        });
    });

    $(document).on('click', '.user-item', function() {
        const userId = $(this).data('id');
        const docId = $('#docId').val();

        $.post('../core/handleForms.php', {
            action: 'share_user',
            doc_id: docId,
            user_id: userId
        }, function(response) {
            const res = JSON.parse(response);
            if (res.status === 'success') {
                alert("User added to document.");
                location.reload();
            } else {
                alert("Failed to share document.");
            }
        });
    });
    </script>
</body>
</html>
