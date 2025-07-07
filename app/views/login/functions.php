<?php
// Generar y validar tokens CSRF
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Manejo de intentos fallidos
function registerLoginAttempt($correo) {
    $_SESSION['login_attempts'][$correo]['last_attempt'] = time();
    if (!isset($_SESSION['login_attempts'][$correo]['count'])) {
        $_SESSION['login_attempts'][$correo]['count'] = 0;
    }
}

function recordFailedAttempt($correo) {
    $_SESSION['login_attempts'][$correo]['count']++;
    $_SESSION['login_attempts'][$correo]['last_attempt'] = time();
}

function resetFailedAttempts($correo) {
    unset($_SESSION['login_attempts'][$correo]);
}

function isAccountLocked($correo) {
    $max_attempts = 5;
    $lock_time = 15 * 60; // 15 minutos
    
    if (isset($_SESSION['login_attempts'][$correo])) {
        $attempts = $_SESSION['login_attempts'][$correo]['count'];
        $last_attempt = $_SESSION['login_attempts'][$correo]['last_attempt'];
        
        if ($attempts >= $max_attempts && (time() - $last_attempt) < $lock_time) {
            return true;
        } elseif ($attempts >= $max_attempts && (time() - $last_attempt) >= $lock_time) {
            resetFailedAttempts($correo);
            return false;
        }
    }
    
    return false;
    return;
}