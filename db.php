<?php
// db.php - Свързване с базата данни

$host = 'localhost';
$dbname = 'yaystay_reservations';
$username = 'root';  
$password = '';      


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Грешка при свързването с базата данни: ' . $e->getMessage());
}
?>