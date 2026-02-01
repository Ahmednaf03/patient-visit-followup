<?php
session_start();
include_once 'helpers/auth.php';

echo '<pre>';
print_r($_SESSION);

print_r(isLoggedIn());
print_r(isAdmin());

if (isAdmin()) {
    header('Location: patients/list.php');
    exit;
}

if (isUser()) {
    header('Location: patients/view.php?id=' . $_SESSION['auth']['patient_id']);
    exit;
}
