<?php
session_start();
require_once '../core/dbConfig.php';
require_once '../core/models.php';

// Only allow admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$adminId = $_SESSION['user_id'];
$users = getAllUsersExcept($adminId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="admin-manage-users">
        <h2>Manage User Accounts</h2>
        <a href="index.php">← Back to Admin Dashboard</a><br><br>

        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Suspended?</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td>
                            <input type="checkbox" class="suspend-toggle" 
                                   data-id="<?= $user['id'] ?>" 
                                   <?= $user['suspended'] ? 'checked' : '' ?>>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
    $('.suspend-toggle').on('change', function() {
        const userId = $(this).data('id');
        const suspend = $(this).is(':checked') ? 1 : 0;

        $.post('../core/handleForms.php', {
            action: 'toggle_suspend',
            user_id: userId,
            suspend: suspend
        }, function(response) {
            const res = JSON.parse(response);
            if (res.status === 'success') {
                alert('User status updated.');
            } else {
                alert('Failed to update user status.');
            }
        });
    });
    </script>
</body>
</html>
