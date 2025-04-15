<?php
$host = 'localhost';
$dbname = 'yaystay_reservations';
$username = 'Yaystay';  
$password = 'Yaystay123';  

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    echo "Свързването с базата е успешно!";
} catch (Exception $e) {
    die('Грешка при свързването с базата данни: ' . $e->getMessage());
}
?>
