<?php
// core/handleForms.php
session_start();
require_once 'dbConfig.php';
require_once 'models.php';

// 🟩 USER LOGIN
if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $user = getUserByEmail($email);

    if ($user && password_verify($password, $user['password'])) {
        if ($user['suspended']) {
            echo json_encode(['status' => 'error', 'message' => 'Account is suspended.']);
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            echo json_encode(['status' => 'success']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid credentials.']);
    }
    exit;
}

// 🟩 USER REGISTRATION
if (isset($_POST['action']) && $_POST['action'] === 'register') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'user';

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, suspended) VALUES (?, ?, ?, ?, 0)");
    $stmt->bind_param("ssss", $name, $email, $password, $role);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Registration failed.']);
    }
    exit;
}

// 🟩 CREATE DOCUMENT
if (isset($_POST['action']) && $_POST['action'] === 'create_document') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $ownerId = $_SESSION['user_id'];

    if (createDocument($title, $content, $ownerId)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Document creation failed.']);
    }
    exit;
}

// 🟩 AUTO-SAVE DOCUMENT CONTENT
if (isset($_POST['action']) && $_POST['action'] === 'autosave') {
    $docId = intval($_POST['doc_id']);
    $content = $_POST['content'];
    $userId = $_SESSION['user_id'];

    if (updateDocumentContent($docId, $content)) {
        logActivity($docId, $userId, "Auto-saved document.");
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit;
}

// 🟩 SHARE DOCUMENT WITH USER
if (isset($_POST['action']) && $_POST['action'] === 'share_user') {
    $docId = intval($_POST['doc_id']);
    $userId = intval($_POST['user_id']);
    if (shareDocumentWithUser($docId, $userId)) {
        logActivity($docId, $_SESSION['user_id'], "Shared document with user ID $userId");
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit;
}

// 🟩 SEARCH USERS
if (isset($_GET['action']) && $_GET['action'] === 'search_user') {
    $term = $_GET['term'];
    $results = searchUsers($term, $_SESSION['user_id']);
    $users = [];

    while ($row = $results->fetch_assoc()) {
        $users[] = $row;
    }

    echo json_encode($users);
    exit;
}

// 🟩 SUSPEND / UNSUSPEND USER (ADMIN)
if (isset($_POST['action']) && $_POST['action'] === 'toggle_suspend') {
    $userId = intval($_POST['user_id']);
    $suspend = intval($_POST['suspend']);
    if (suspendUser($userId, $suspend)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit;
}

// 🟩 SEND MESSAGE
if (isset($_POST['action']) && $_POST['action'] === 'send_message') {
    $docId = intval($_POST['doc_id']);
    $message = trim($_POST['message']);
    $senderId = $_SESSION['user_id'];

    if (sendMessage($docId, $senderId, $message)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit;
}

// 🟩 LOGOUT
if (isset($_POST['action']) && $_POST['action'] === 'logout') {
    session_destroy();
    echo json_encode(['status' => 'success']);
    exit;
}
