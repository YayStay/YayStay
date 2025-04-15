<?php
require '../db.php';

try {
    $new_password = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE username = 'admin'");
    $stmt->execute([$new_password]);
    
    echo "Паролата на администратора е актуализирана.<br>";
    echo "Kullanıcı adı: admin<br>";
    echo "Şifre: admin123";
    
} catch (PDOException $e) {
    echo "Hata: " . $e->getMessage();
}
