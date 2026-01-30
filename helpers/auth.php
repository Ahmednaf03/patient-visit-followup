<?php

function isAdmin(): bool{
    return isset($_SESSION['admin_id']);
}

function requireAdmin(): void{
    if (!isAdmin()) {
        header('Location: ../includes/login.php');
        exit;
    }
}
