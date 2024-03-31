<?php
$host = "localhost";
$db_name = "juanxyz_yt";
$username = "juanxyz";
$password = "G02X3.4yiHvR-g";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
