<?php
session_start();
require_once '../helpers/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/styles.css">
    <style>
        

</style>

</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
   <div class="container">
    <a class="navbar-brand" href="../index.php">Healthcare System</a>

    <div class="navbar-nav">

        <!-- Admin Login / Logout (FIRST) -->
        <?php if (isAdmin()): ?>
            <a class="nav-link" href="../includes/logout.php">
                Logout (<?= htmlspecialchars($_SESSION['admin_name']) ?>)
            </a>
        <?php else: ?>
            <a class="nav-link" href="../includes/login.php">Admin Login</a>
        <?php endif; ?>

        <a class="nav-link" href="../patients/list.php">Patients</a>

        <!-- Add Patient only for admin -->
        <?php if (isAdmin()): ?>
            <a class="nav-link" href="../patients/addpatient.php">Add Patient</a>
        <?php endif; ?>

        <a class="nav-link" href="../visits/visitlist.php">Visits</a>
        <a class="nav-link" href="../reports/summary.php">Reports</a>
    </div>
</div>

    </div>
</nav>

<div class="container">
