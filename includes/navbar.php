<?php
if (!isset($_SESSION)) session_start();

// Optional: Include DB and model functions if you need counts or logic
// require_once '../core/dbConfig.php';
// require_once '../core/models.php';
?>

<!-- Bootstrap CDN (Add only once in layout, skip if already loaded) -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<nav class="navbar navbar-expand-lg navbar-dark p-4" style="background-color: #008080;">
  <a class="navbar-brand" href="#">
    <?= ($_SESSION['role'] ?? 'Guest') === 'admin' ? 'Admin Panel' : 'Google Docs Clone' ?>
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      
      <?php if (isset($_SESSION['user_id'])): ?>
        <?php if ($_SESSION['role'] === 'admin'): ?>
          <li class="nav-item"><a class="nav-link" href="/googledocs_clone/admin/index.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="/googledocs_clone/admin/manage_users.php">Manage Users</a></li>
          <li class="nav-item"><a class="nav-link" href="/googledocs_clone/admin/view_all_docs.php">View All Docs</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="/googledocs_clone/users/index.php">My Documents</a></li>
          <li class="nav-item"><a class="nav-link" href="/googledocs_clone/users/create_doc.php">Create Document</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link" href="/googledocs_clone/logout.php">Logout</a></li>
      <?php else: ?>
        <li class="nav-item"><a class="nav-link" href="/googledocs_clone/index.php">Login</a></li>
        <li class="nav-item"><a class="nav-link" href="/googledocs_clone/register.php">Register</a></li>
      <?php endif; ?>
      
    </ul>
  </div>
</nav>
