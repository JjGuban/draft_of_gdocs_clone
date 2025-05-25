<?php
session_start();
require_once '../core/dbConfig.php';
require_once '../core/models.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// Define user role safely from session
$userRole = $_SESSION['role'] ?? 'user';

$docId = isset($_GET['doc_id']) ? intval($_GET['doc_id']) : 0;
$document = getDocumentById($docId);
$userId = $_SESSION['user_id'];

// Check if document exists
if (!$document) {
    die("Document not found.");
}

// Check owner
$isOwner = ($document['owner_id'] == $userId);

// Check shared users properly
$isSharedUser = false;
$sharedResult = getSharedUsers($docId);
if ($sharedResult) {
    while ($row = $sharedResult->fetch_assoc()) {
        if ($row['id'] == $userId) {
            $isSharedUser = true;
            break;
        }
    }
}

// Access control: allow if owner, shared user, or admin
if (!$isOwner && !$isSharedUser && $userRole !== 'admin') {
    die("Access denied. You do not have permission to view this log.");
}

// Retrieve logs
$logs = getActivityLogs($docId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Activity Logs - <?= htmlspecialchars($document['title']) ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="logs-container">
        <h2>Activity Logs for: <?= htmlspecialchars($document['title']) ?></h2>
        <a href="index.php">← Back to Dashboard</a>

        <?php if ($logs && $logs->num_rows > 0): ?>
            <table border="1" cellpadding="10" cellspacing="0">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($log = $logs->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($log['name']) ?></td>
                            <td><?= htmlspecialchars($log['action']) ?></td>
                            <td><?= $log['timestamp'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No activity logs found for this document.</p>
        <?php endif; ?>
    </div>
</body>
</html>
