<?php
// templates/freelancer/messages_content.php

require_once __DIR__ . '/../../includes/db_connect.php'; // Path to db_connect.php from templates/freelancer/

$current_freelancer_id = 2; // Hardcoded for Marcus Finch (acting as freelancer)
// In a real app, this would come from a session: e.g., $_SESSION['user_id'] after login.

$conversations = [];
$active_chat_messages = [];
$active_chat_with_user = null; // Stores user details of the person being chatted with (this is the one used by the template)
$active_chat_user_id = null;  // The ID of the other user in the active chat (used for queries)

// Fetch conversations list:
// Users with whom $current_freelancer_id has exchanged messages, along with the latest message.
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
    // Bind $current_freelancer_id to all placeholders
    $stmt_conversations->bind_param("iiiiiii", $current_freelancer_id, $current_freelancer_id, $current_freelancer_id, $current_freelancer_id, $current_freelancer_id, $current_freelancer_id, $current_freelancer_id);
    $stmt_conversations->execute();
    $result_conversations = $stmt_conversations->get_result();
    while ($row = $result_conversations->fetch_assoc()) {
        $conversations[] = $row;
    }
    $stmt_conversations->close();
} else {
    error_log("Error preparing freelancer conversations statement: " . $conn->error);
}

// Determine the active chat partner ID.
if (isset($_GET['chat_with']) && is_numeric($_GET['chat_with'])) {
    $potential_partner_id = (int)$_GET['chat_with'];
    // Validate that this potential partner is actually in a conversation with the freelancer
    $is_valid_partner = false;
    foreach ($conversations as $conv) {
        if ($conv['user_id'] == $potential_partner_id) {
            $is_valid_partner = true;
            break;
        }
    }
    if ($is_valid_partner) {
        $active_chat_user_id = $potential_partner_id;
    }
}

// If no valid 'chat_with' is provided, default to the first conversation (if any)
if ($active_chat_user_id === null && !empty($conversations)) {
    $active_chat_user_id = $conversations[0]['user_id'];
}


// Fetch messages and partner details for the active conversation if a partner is identified
if ($active_chat_user_id !== null) {
    // Find the user details for the active chat partner from the already fetched conversations list
    foreach ($conversations as $conv) {
        if ($conv['user_id'] == $active_chat_user_id) {
            $active_chat_with_user = $conv; // Contains user_id, full_name, avatar_url, role
            break;
        }
    }
    // If $active_chat_with_user is still null (e.g. chat_with ID was invalid and not in conversation list)
    // we might need an explicit query for user details, but this indicates an issue if it happens.
    // For now, we rely on $conversations list for partner details.

    // SQL to fetch messages between $current_freelancer_id and $active_chat_user_id
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
        $stmt_messages->bind_param("iiii", $current_freelancer_id, $active_chat_user_id, $active_chat_user_id, $current_freelancer_id);
        $stmt_messages->execute();
        $result_messages = $stmt_messages->get_result();
        while ($row = $result_messages->fetch_assoc()) {
            $active_chat_messages[] = $row;
        }
        $stmt_messages->close();
    } else {
        error_log("Error preparing freelancer messages statement: " . $conn->error);
    }

    // Mark messages from this partner as read for the current freelancer
    $sql_mark_read = "UPDATE messages SET is_read = TRUE WHERE receiver_id = ? AND sender_id = ? AND is_read = FALSE";
    if ($stmt_mark_read = $conn->prepare($sql_mark_read)) {
        $stmt_mark_read->bind_param("ii", $current_freelancer_id, $active_chat_user_id);
        $stmt_mark_read->execute();
        $stmt_mark_read->close();
        // Note: Unread counts in the $conversations array might be stale for this request after marking as read.
        // This could be handled by re-fetching or updating client-side via JS in a more advanced setup.
        // For this PHP-driven version, counts will be correct on next full load/navigation.
    } else {
        error_log("Error marking messages as read for freelancer: " . $conn->error);
    }
}

// Make $current_user_id available for the main template (freelancer_dashboard.php)
// The message display template uses $current_user_id to determine if a message is outgoing or incoming.
// So, we set $current_user_id to $current_freelancer_id for this context.
$current_user_id = $current_freelancer_id;

// Variables available to freelancer_dashboard.php:
// $conversations
// $active_chat_messages
// $active_chat_with_user (contains details of the other user in the active chat)
// $active_chat_user_id (ID of the other user in the active chat)
// $current_user_id (set to $current_freelancer_id for message styling logic)

?>
