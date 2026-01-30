<?php
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    die("Invalid patient ID");
}

header("Location: addpatient.php?id=" . $id);
exit;
?>