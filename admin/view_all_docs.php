<?php
session_start();
require_once '../core/dbConfig.php';
require_once '../core/models.php';

// Ensure only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$admin = getUserById($_SESSION['user_id']);
$allDocs = getAllDocuments();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Documents - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="view-all-docs">
        <h2>All Documents in the System</h2>
        <a href="index.php">← Back to Admin Dashboard</a><br><br>

        <?php if ($allDocs->num_rows > 0): ?>
            <table border="1" cellpadding="10" cellspacing="0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Owner</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($doc = $allDocs->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($doc['title']) ?></td>
                            <td><?= htmlspecialchars($doc['owner_name']) ?></td>
                            <td><?= $doc['updated_at'] ?></td>
                            <td>
                                <?php
                                // Admin can only edit if it’s shared to them
                                if ($doc['owner_id'] == $_SESSION['user_id']) {
                                    echo "<a href='../users/edit_doc.php?doc_id={$doc['id']}'>Edit</a> | ";
                                }
                                ?>
                                <a href="../users/activity_logs.php?doc_id=<?= $doc['id'] ?>">Logs</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No documents found in the system.</p>
        <?php endif; ?>
    </div>
</body>
</html>
