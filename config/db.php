<?php
define('BASE_URL', '/healthcare_records_db');   // because your URL is localhost/healthcare_records_db

$host = "localhost";
$dbname = "healthcare_records_db";
$username = "root";
$password = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password
    );

    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // echo "Connected successfully <br>";

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>