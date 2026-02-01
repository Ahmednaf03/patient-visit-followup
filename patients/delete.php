<?php
require_once '../config/db.php';
require_once '../helpers/auth.php';

requireAdmin();// checks if admin is logged in else redirects to login

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request');
}

$patientId = (int)$_GET['id'];


$stmt = $pdo->prepare("DELETE FROM patients WHERE patient_id = ?");
$stmt->execute([$patientId]);

header('Location: list.php');
exit;
