<?php
require '../db.php';

try {
    $pdo->exec("DELETE FROM admin_users WHERE username = 'admin'");
    
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
    $result = $stmt->execute(['admin', $password]);
    
    if ($result) {
        echo "Създаден е тестов администраторски акаунт:<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
    }
    
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute(['admin']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<pre>";
    print_r($user);
    echo "</pre>";
    
} catch (PDOException $e) {
    echo "ГРЕШКА: " . $e->getMessage();
}
?>
