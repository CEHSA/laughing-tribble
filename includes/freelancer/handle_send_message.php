<?php
// includes/freelancer/handle_send_message.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Path is from includes/freelancer/ up one level to includes/, then db_connect.php
require_once __DIR__ . '/../db_connect.php';

// --- Configuration & Hardcoded values ---
$sender_id = 2; // Hardcoded: Represents Marcus Finch (acting as freelancer)
// In a real app, this would come from $_SESSION['user_id'] or similar.

// --- Retrieve Form Data ---
$message_content = isset($_POST['message_content']) ? trim($_POST['message_content']) : '';
$receiver_id = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 0;
// current_view_url should be like "index.php?page=freelancer_dashboard&view=messages&chat_with=USER_ID"
$current_view_url = isset($_POST['current_view_url']) ? $_POST['current_view_url'] : '';

// --- Basic Validation ---
$errors = [];
if (empty($message_content)) {
    $errors[] = "Message content cannot be empty.";
}
if ($receiver_id <= 0) {
    $errors[] = "Invalid receiver ID.";
}
if (empty($current_view_url)) {
    $errors[] = "Return URL not specified.";
    // Fallback redirect if current_view_url is missing
    $current_view_url = 'index.php?page=freelancer_dashboard&view=messages';
}


// --- Process Message Insertion ---
if (empty($errors)) {
    $sql = "INSERT INTO messages (sender_id, receiver_id, message_content, timestamp, is_read) VALUES (?, ?, ?, NOW(), FALSE)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("iis", $sender_id, $receiver_id, $message_content);
        if ($stmt->execute()) {
            // Success
            // $_SESSION['success_message'] = "Message sent!"; // Optional feedback
        } else {
            // $_SESSION['error_message'] = "Database error sending message: " . $stmt->error;
            error_log("Freelancer send message DB error: " . $stmt->error);
        }
        $stmt->close();
    } else {
        // $_SESSION['error_message'] = "Database error preparing message: " . $conn->error;
        error_log("Freelancer send message prepare error: " . $conn->error);
    }
} else {
    // $_SESSION['error_message'] = implode("<br>", $errors);
    error_log("Freelancer send message validation errors: " . implode(", ", $errors));
}

$conn->close();

// --- Redirect User ---
// Path adjustment: from 'includes/freelancer/' up two levels to project root for index.php
$redirect_path = "../../" . $current_view_url;

// Ensure no output before header()
if (!headers_sent()) {
    header("Location: " . $redirect_path);
    exit;
} else {
    // Fallback if headers already sent (e.g., due to an error message printed by PHP)
    echo "<p>Error: Could not redirect. <a href=\"" . htmlspecialchars($redirect_path) . "\">Click here to return</a>.</p>";
    error_log("Headers already sent in includes/freelancer/handle_send_message.php. Redirect path: " . $redirect_path);
}
?>
