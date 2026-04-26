<?php
// ============================================
// Aurex - Admin Logout
// ============================================
session_start();
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);
session_regenerate_id(true);
header('Location: index.php');
exit;
