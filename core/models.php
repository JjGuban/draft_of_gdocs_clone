<?php
// core/models.php
require_once 'dbConfig.php';

// 🔒 AUTH FUNCTIONS
function getUserByEmail($email) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getUserById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function isUserSuspended($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT suspended FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($suspended);
    $stmt->fetch();
    return $suspended == 1;
}

// 📄 DOCUMENT FUNCTIONS
function createDocument($title, $content, $ownerId) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO documents (title, content, owner_id, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("ssi", $title, $content, $ownerId);
    return $stmt->execute();
}

function getUserDocuments($userId) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT d.* FROM documents d
        LEFT JOIN shared_documents sd ON d.id = sd.doc_id
        WHERE d.owner_id = ? OR sd.user_id = ?
        GROUP BY d.id
    ");
    $stmt->bind_param("ii", $userId, $userId);
    $stmt->execute();
    return $stmt->get_result();
}

function getAllDocuments() {
    global $conn;
    $query = "SELECT d.*, u.name AS owner_name FROM documents d JOIN users u ON d.owner_id = u.id";
    return $conn->query($query);
}

function getDocumentById($docId) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM documents WHERE id = ?");
    $stmt->bind_param("i", $docId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateDocumentContent($docId, $newContent) {
    global $conn;
    $stmt = $conn->prepare("UPDATE documents SET content = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $newContent, $docId);
    return $stmt->execute();
}

// 👥 SHARING FUNCTIONS
function searchUsers($search, $excludeId) {
    global $conn;
    $like = "%$search%";
    $stmt = $conn->prepare("SELECT id, name, email FROM users WHERE (name LIKE ? OR email LIKE ?) AND id != ?");
    $stmt->bind_param("ssi", $like, $like, $excludeId);
    $stmt->execute();
    return $stmt->get_result();
}

function shareDocumentWithUser($docId, $userId) {
    global $conn;
    $stmt = $conn->prepare("INSERT IGNORE INTO shared_documents (doc_id, user_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $docId, $userId);
    return $stmt->execute();
}

function getSharedUsers($docId) {
    global $conn;
    $stmt = $conn->prepare("SELECT u.id, u.name, u.email FROM shared_documents sd JOIN users u ON sd.user_id = u.id WHERE sd.doc_id = ?");
    $stmt->bind_param("i", $docId);
    $stmt->execute();
    return $stmt->get_result();
}

// 📝 ACTIVITY LOGS
function logActivity($docId, $userId, $action) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO activity_logs (doc_id, user_id, action, timestamp) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $docId, $userId, $action);
    return $stmt->execute();
}

function getActivityLogs($docId) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT al.*, u.name FROM activity_logs al
        JOIN users u ON al.user_id = u.id
        WHERE al.doc_id = ?
        ORDER BY al.timestamp DESC
    ");
    $stmt->bind_param("i", $docId);
    $stmt->execute();
    return $stmt->get_result();
}

// 💬 MESSAGES
function sendMessage($docId, $senderId, $message) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO messages (doc_id, sender_id, message, sent_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $docId, $senderId, $message);
    return $stmt->execute();
}

function getMessages($docId) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT m.*, u.name FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE m.doc_id = ?
        ORDER BY m.sent_at ASC
    ");
    $stmt->bind_param("i", $docId);
    $stmt->execute();
    return $stmt->get_result();
}

// ⚙️ ADMIN FUNCTIONS
function suspendUser($userId, $status) {
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET suspended = ? WHERE id = ?");
    $stmt->bind_param("ii", $status, $userId);
    return $stmt->execute();
}

function getAllUsersExcept($adminId) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE id != ?");
    $stmt->bind_param("i", $adminId);
    $stmt->execute();
    return $stmt->get_result();
}
