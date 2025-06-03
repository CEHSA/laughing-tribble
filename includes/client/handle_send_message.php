<?php
// includes/client/handle_send_message.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../db_connect.php'; // Connect to the database

// --- Configuration & Hardcoded values ---
$sender_id = 1; // Hardcoded: Represents Eleanor Vance (current logged-in user)

// --- Retrieve Form Data ---
$message_content = isset($_POST['message_content']) ? trim($_POST['message_content']) : '';
$receiver_id = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 0;
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
    // Fallback if the redirect URL isn't provided, though it should be.
    // Redirect to a generic messages page or dashboard.
    // For now, this is an error condition for robustness.
    $errors[] = "Return URL not specified.";
    // Default redirect if something goes wrong with current_view_url
    $current_view_url = 'index.php?page=client_dashboard&view=messages';
}


// --- Process Message Insertion ---
if (empty($errors)) {
    // Prepare SQL INSERT statement for the messages table
    $sql = "INSERT INTO messages (sender_id, receiver_id, message_content, timestamp, is_read) VALUES (?, ?, ?, NOW(), FALSE)";

    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters: sender_id, receiver_id, message_content
        $stmt->bind_param("iis", $sender_id, $receiver_id, $message_content);

        // Execute the statement
        if ($stmt->execute()) {
            // Message inserted successfully
            // Store a success message in session (optional)
            // $_SESSION['success_message'] = "Message sent successfully!";
        } else {
            // Error during insertion
            // $_SESSION['error_message'] = "Error sending message: " . $stmt->error;
            error_log("Error inserting message: " . $stmt->error); // Log actual DB error
        }
        $stmt->close();
    } else {
        // Error preparing statement
        // $_SESSION['error_message'] = "Error preparing to send message: " . $conn->error;
        error_log("Error preparing message insert statement: " . $conn->error); // Log actual DB error
    }
} else {
    // Handle validation errors
    // $_SESSION['error_message'] = implode("<br>", $errors);
    // For now, just log them if any. Redirect will happen anyway.
    error_log("Validation errors sending message: " . implode(", ", $errors));
}

$conn->close();

// --- Redirect User ---
// Adjust path from 'includes/client/' up two levels to project root for index.php
// The $current_view_url should be like "index.php?page=client_dashboard&view=messages&chat_with=USER_ID"
if (strpos($current_view_url, 'index.php') === 0) {
    // current_view_url already seems to be relative to root
    $redirect_path = "../../" . $current_view_url; // Path from includes/client/ to root
} else {
    // Fallback if current_view_url is not as expected, construct a default one.
    // This case should ideally not happen if the form provides the correct URL.
    $default_redirect = "index.php?page=client_dashboard&view=messages";
    if ($receiver_id > 0) {
        $default_redirect .= "&chat_with=" . $receiver_id;
    }
    $redirect_path = "../../" . $default_redirect;
}
// Ensure no output before header()
ob_start();
header("Location: " . $redirect_path);
ob_end_flush(); // Send the output buffer and turn off output buffering
exit;

?>
