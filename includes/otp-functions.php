<?php
// ============================================
// Aurex - OTP Verification Functions
// ============================================

/**
 * Generate a 6-digit OTP
 */
function generateOTP() {
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

/**
 * Send OTP - stores in database (in production, integrate SMS/email API)
 */
function sendOTP($pdo, $identifier, $purpose = 'register') {
    // Determine if identifier is email or phone
    $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);

    // Invalidate any previous unused OTPs
    if ($isEmail) {
        $stmt = $pdo->prepare("UPDATE otp_verification SET is_verified = 1 WHERE email = :email AND purpose = :purpose AND is_verified = 0");
        $stmt->execute([':email' => $identifier, ':purpose' => $purpose]);
    } else {
        $stmt = $pdo->prepare("UPDATE otp_verification SET is_verified = 1 WHERE phone = :phone AND purpose = :purpose AND is_verified = 0");
        $stmt->execute([':phone' => $identifier, ':purpose' => $purpose]);
    }

    // Generate new OTP
    $otp = generateOTP();
    $expiresAt = date('Y-m-d H:i:s', strtotime('+5 minutes'));

    // Store OTP
    $stmt = $pdo->prepare("INSERT INTO otp_verification (email, phone, otp_code, purpose, expires_at) VALUES (:email, :phone, :otp, :purpose, :expires)");
    $stmt->execute([
        ':email' => $isEmail ? $identifier : null,
        ':phone' => $isEmail ? null : $identifier,
        ':otp' => $otp,
        ':purpose' => $purpose,
        ':expires' => $expiresAt
    ]);

    // In production: send OTP via SMS/Email API here
    // For development: store in session for display
    $_SESSION['dev_otp'] = $otp;
    $_SESSION['otp_identifier'] = $identifier;

    return ['success' => true, 'otp' => $otp]; // Return OTP for dev purposes
}

/**
 * Verify OTP
 */
function verifyOTP($pdo, $identifier, $otpCode, $purpose = 'register') {
    $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);

    if ($isEmail) {
        $stmt = $pdo->prepare("SELECT * FROM otp_verification WHERE email = :identifier AND otp_code = :otp AND purpose = :purpose AND is_verified = 0 AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1");
    } else {
        $stmt = $pdo->prepare("SELECT * FROM otp_verification WHERE phone = :identifier AND otp_code = :otp AND purpose = :purpose AND is_verified = 0 AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1");
    }

    $stmt->execute([
        ':identifier' => $identifier,
        ':otp' => $otpCode,
        ':purpose' => $purpose
    ]);

    $record = $stmt->fetch();

    if ($record) {
        // Mark as verified
        $updateStmt = $pdo->prepare("UPDATE otp_verification SET is_verified = 1 WHERE id = :id");
        $updateStmt->execute([':id' => $record['id']]);
        return true;
    }

    return false;
}
