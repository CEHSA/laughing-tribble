<?php
// templates/client/messages_content.php

require_once __DIR__ . '/../../includes/db_connect.php'; // Adjusted path

$current_user_id = 1; // Hardcoded for Eleanor Vance

$conversations = [];
$active_chat_messages = [];
$active_chat_with_user = null; // Stores user details of the person being chatted with

// Fetch conversations list:
// Users with whom $current_user_id has exchanged messages, along with the latest message.
// This query is a bit complex. It aims to find unique users $current_user_id has talked to,
// and the latest message exchanged with each of them.

$sql_conversations = "
    SELECT
        u.user_id,
        u.full_name,
        u.avatar_url,
        u.role,
        m_latest.message_content AS latest_message,
        m_latest.timestamp AS latest_message_timestamp,
        (SELECT COUNT(*) FROM messages m_unread
         WHERE m_unread.receiver_id = ?
           AND m_unread.sender_id = u.user_id
           AND m_unread.is_read = FALSE) AS unread_count
    FROM (
        SELECT
            CASE
                WHEN sender_id = ? THEN receiver_id
                ELSE sender_id
            END AS other_user_id,
            MAX(timestamp) AS max_timestamp
        FROM messages
        WHERE sender_id = ? OR receiver_id = ?
        GROUP BY other_user_id
    ) AS last_interaction
    JOIN users u ON u.user_id = last_interaction.other_user_id
    JOIN messages m_latest ON (
        (m_latest.sender_id = ? AND m_latest.receiver_id = u.user_id) OR
        (m_latest.sender_id = u.user_id AND m_latest.receiver_id = ?)
    ) AND m_latest.timestamp = last_interaction.max_timestamp
    WHERE u.user_id != ? -- Exclude self from conversation list
    ORDER BY latest_message_timestamp DESC;
";

if ($stmt_conversations = $conn->prepare($sql_conversations)) {
    // Bind $current_user_id to all placeholders
    $stmt_conversations->bind_param("iiiiiii", $current_user_id, $current_user_id, $current_user_id, $current_user_id, $current_user_id, $current_user_id, $current_user_id);
    $stmt_conversations->execute();
    $result_conversations = $stmt_conversations->get_result();
    while ($row = $result_conversations->fetch_assoc()) {
        $conversations[] = $row;
    }
    $stmt_conversations->close();
} else {
    // Handle error - e.g., log it
    error_log("Error preparing conversations statement: " . $conn->error);
}


// Determine the active chat partner.
// For now, if conversations exist, pick the first one.
// Later, this could be driven by a GET parameter (e.g., ?view=messages&chat_with=USER_ID)
$active_chat_partner_id = null;
if (!empty($conversations)) {
    // Check if a 'chat_with' GET parameter is set and is a valid conversation partner
    if (isset($_GET['chat_with']) && is_numeric($_GET['chat_with'])) {
        $potential_partner_id = (int)$_GET['chat_with'];
        foreach ($conversations as $conv) {
            if ($conv['user_id'] == $potential_partner_id) {
                $active_chat_partner_id = $potential_partner_id;
                break;
            }
        }
    }
    // If 'chat_with' is not set or invalid, default to the first conversation
    if ($active_chat_partner_id === null) {
        $active_chat_partner_id = $conversations[0]['user_id'];
    }
}


// Fetch messages for the active conversation if a partner is identified
if ($active_chat_partner_id !== null) {
    // Find the user details for the active chat partner
    foreach ($conversations as $conv) {
        if ($conv['user_id'] == $active_chat_partner_id) {
            $active_chat_with_user = $conv; // Contains user_id, full_name, avatar_url, role
            break;
        }
    }

    // SQL to fetch messages between $current_user_id and $active_chat_partner_id
    $sql_messages = "
        SELECT m.message_id, m.sender_id, m.receiver_id, m.message_content, m.timestamp,
               s.full_name AS sender_name, s.avatar_url AS sender_avatar,
               r.full_name AS receiver_name, r.avatar_url AS receiver_avatar
        FROM messages m
        JOIN users s ON m.sender_id = s.user_id
        JOIN users r ON m.receiver_id = r.user_id
        WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY m.timestamp ASC;
    ";

    if ($stmt_messages = $conn->prepare($sql_messages)) {
        $stmt_messages->bind_param("iiii", $current_user_id, $active_chat_partner_id, $active_chat_partner_id, $current_user_id);
        $stmt_messages->execute();
        $result_messages = $stmt_messages->get_result();
        while ($row = $result_messages->fetch_assoc()) {
            $active_chat_messages[] = $row;
        }
        $stmt_messages->close();
    } else {
        // Handle error
        error_log("Error preparing messages statement: " . $conn->error);
    }

    // Mark messages from this partner as read
    $sql_mark_read = "UPDATE messages SET is_read = TRUE WHERE receiver_id = ? AND sender_id = ? AND is_read = FALSE";
    if ($stmt_mark_read = $conn->prepare($sql_mark_read)) {
        $stmt_mark_read->bind_param("ii", $current_user_id, $active_chat_partner_id);
        $stmt_mark_read->execute();
        $stmt_mark_read->close();
        // Re-fetch conversations to update unread counts if needed, or update in JS.
        // For this PHP-driven version, the unread count will be correct on next full page load/refresh.
    }
}

// The variables $conversations, $active_chat_messages, $active_chat_with_user, and $current_user_id
// are now set and will be available in the scope of templates/client_dashboard.php
// when this file is included.

// Note: The path to db_connect.php was adjusted assuming messages_content.php is in templates/client/
// and db_connect.php is in includes/.
// The __DIR__ magic constant helps in making paths relative to the current file.
?>
