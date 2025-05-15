<?php
// config.php for PostgreSQL
$host = 'localhost';
$db   = 'IOT';
$user = 'postgres';
$pass = 'root';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
