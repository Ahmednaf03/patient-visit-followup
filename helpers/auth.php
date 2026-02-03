<?php
// if (session_status() === PHP_SESSION_NONE) {
    // session_start();
// }



function isLoggedIn(): bool {
    return isset($_SESSION['auth']);
}

function isAdmin(): bool {
    return isLoggedIn() && $_SESSION['auth']['role'] === 'admin';
}

function isUser(): bool {
    return isLoggedIn() && $_SESSION['auth']['role'] === 'user';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /patient-visit-followup/includes/login.php');
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        http_response_code(403);
        exit('Forbidden, admin access only go back <a href="/patient-visit-followup/index.php" >Home</a>');
    }
}
