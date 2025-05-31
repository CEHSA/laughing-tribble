<?php

require_once __DIR__ . '/../config/database.php'; // Include database configuration

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PUT, OPTIONS, DELETE"); // Added DELETE
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle OPTIONS request (pre-flight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];
$pdo = get_db_connection_to_db(); // Establish database connection

if (!$pdo) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed. Please check server logs.']);
    exit;
}

// === REGISTRATION ===
if (strpos($requestUri, '/register') !== false && $requestMethod == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['username']) || empty($data['password']) || empty($data['email']) || empty($data['role'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields: username, password, email, role.']);
        exit;
    }
    if (!in_array($data['role'], ['Client', 'Freelancer'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid role. Must be "Client" or "Freelancer".']);
        exit;
    }

    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, bio, skills, profile_picture_url) VALUES (:username, :email, :password, :role, :bio, :skills, :profile_picture_url)");
        $stmt->execute([
            ':username' => $data['username'],
            ':email' => $data['email'],
            ':password' => $hashedPassword,
            ':role' => $data['role'],
            ':bio' => $data['bio'] ?? null,
            ':skills' => isset($data['skills']) && is_array($data['skills']) ? json_encode($data['skills']) : null,
            ':profile_picture_url' => $data['profile_picture_url'] ?? null
        ]);
        $userId = $pdo->lastInsertId();
        http_response_code(201);
        echo json_encode(['status' => 'success', 'message' => 'User registered successfully.', 'user_id' => $userId]);
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') { // Integrity constraint violation (duplicate)
            http_response_code(409); // Conflict
            echo json_encode(['status' => 'error', 'message' => 'Username or email already exists.']);
        } else {
            http_response_code(500);
            error_log("Registration PDOException: " . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Registration failed. Please try again later.']);
        }
    }
    exit;
}

// === LOGIN ===
elseif (strpos($requestUri, '/login') !== false && $requestMethod == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['email']) || empty($data['password'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields: email, password.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, username, email, password, role FROM users WHERE email = :email");
        $stmt->execute([':email' => $data['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($data['password'], $user['password'])) {
            // Passwords match
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'message' => 'Login successful.',
                'token' => 'fake-jwt-token-' . bin2hex(random_bytes(16)), // Still a fake token
                'user' => [ // Send back some user info, exclude password
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ]
            ]);
        } else {
            http_response_code(401); // Unauthorized
            echo json_encode(['status' => 'error', 'message' => 'Invalid email or password.']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        error_log("Login PDOException: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Login failed. Please try again later.']);
    }
    exit;
}

// === GET USER PROFILE ===
elseif (strpos($requestUri, '/profile') !== false && $requestMethod == 'GET') {
    // For now, user_id must be passed as a query parameter
    // Later, this should be derived from an authentication token
    if (!isset($_GET['user_id']) || !filter_var($_GET['user_id'], FILTER_VALIDATE_INT)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Valid user_id parameter is required.']);
        exit;
    }
    $userId = (int)$_GET['user_id'];

    try {
        $stmt = $pdo->prepare("SELECT id, username, email, role, bio, skills, profile_picture_url, created_at, updated_at FROM users WHERE id = :id");
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (isset($user['skills'])) { // Skills are stored as JSON string
                $user['skills'] = json_decode($user['skills'], true) ?: [];
            }
            http_response_code(200);
            echo json_encode(['status' => 'success', 'user' => $user]);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'User not found.']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        error_log("Get Profile PDOException: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Could not fetch profile. Please try again later.']);
    }
    exit;
}

// === UPDATE USER PROFILE ===
elseif (strpos($requestUri, '/profile') !== false && ($requestMethod == 'PUT' || $requestMethod == 'POST')) {
    $data = json_decode(file_get_contents('php://input'), true);

    // User ID must be provided in the request body for now
    // Later, this should come from the authenticated user's session/token
    if (!isset($data['user_id']) || !filter_var($data['user_id'], FILTER_VALIDATE_INT)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Valid user_id is required in the request body.']);
        exit;
    }
    $userId = (int)$data['user_id'];

    // Fields that can be updated by the user
    $allowedFields = ['username', 'email', 'bio', 'skills', 'profile_picture_url'];
    $updateFields = [];
    $params = [':id' => $userId];

    foreach ($allowedFields as $field) {
        if (isset($data[$field])) {
            $updateFields[] = "`$field` = :$field";
            if ($field === 'skills' && is_array($data[$field])) {
                $params[":$field"] = json_encode($data[$field]);
            } else {
                $params[":$field"] = $data[$field];
            }
        }
    }

    // Password update (simplified: only if new_password is provided, no old_password check here yet)
    // For a real app, add old_password verification before allowing password change.
    if (!empty($data['new_password'])) {
        // Basic validation for new password (e.g., length) should be added
        if (strlen($data['new_password']) < 6) {
             http_response_code(400);
             echo json_encode(['status' => 'error', 'message' => 'New password must be at least 6 characters long.']);
             exit;
        }
        $updateFields[] = "`password` = :password";
        $params[':password'] = password_hash($data['new_password'], PASSWORD_DEFAULT);
    }

    if (empty($updateFields)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'No updatable fields provided or fields are not allowed for update.']);
        exit;
    }

    $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = :id";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully.']);
        } else {
            // This can happen if data submitted is the same as current data, or user_id not found.
            // Check if user exists to give a more specific message.
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE id = :id");
            $checkStmt->execute([':id' => $userId]);
            if (!$checkStmt->fetch()) {
                 http_response_code(404);
                 echo json_encode(['status' => 'error', 'message' => 'User not found, no update performed.']);
            } else {
                 http_response_code(200); // Or 304 Not Modified, but 200 is fine.
                 echo json_encode(['status' => 'success', 'message' => 'Profile data was the same, no changes made.']);
            }
        }
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') { // Duplicate username/email
            http_response_code(409);
            echo json_encode(['status' => 'error', 'message' => 'Update failed: Username or email already exists.']);
        } else {
            http_response_code(500);
            error_log("Update Profile PDOException: " . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Profile update failed. Please try again later.']);
        }
    }
    exit;
}

// Fallback for unknown actions or methods
http_response_code(404);
echo json_encode(['status' => 'error', 'message' => 'User API action not found or method not supported.']);
exit;

?>
