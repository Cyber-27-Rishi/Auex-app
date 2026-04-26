<?php
// ============================================
// Aurex - Authentication Handler
// ============================================

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/otp-functions.php';

/**
 * Register a new user
 */
function registerUser($pdo, $name, $email, $phone, $password) {
    // Check if email or phone already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email OR phone = :phone");
    $stmt->execute([':email' => $email, ':phone' => $phone]);

    if ($stmt->fetch()) {
        return ['success' => false, 'error' => 'Email or phone already registered'];
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password) VALUES (:name, :email, :phone, :password)");
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':phone' => $phone,
        ':password' => $hashedPassword
    ]);

    return ['success' => true, 'user_id' => $pdo->lastInsertId()];
}

/**
 * Login user
 */
function loginUser($pdo, $identifier, $password) {
    // Find by email or phone
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :identifier OR phone = :identifier");
    $stmt->execute([':identifier' => $identifier]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        return ['success' => true, 'user' => $user];
    }

    return ['success' => false, 'error' => 'Invalid credentials'];
}

/**
 * Logout user
 */
function logoutUser() {
    unset($_SESSION['user_id']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_email']);
    session_regenerate_id(true);
}

/**
 * Get current user data
 */
function getCurrentUser($pdo) {
    if (!isLoggedIn()) return null;

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute([':id' => $_SESSION['user_id']]);
    return $stmt->fetch();
}
