<?php
require '../db.php';

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM admin_users");
    $count = $stmt->fetchColumn();
    echo "Admin users in database: " . $count .
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute(['admin']);
    $user = $stmt->fetch();
    echo "Admin user exists: " . ($user ? 'Yes' : 'No') . "<br>";
    
    if ($user) {
        echo "Admin user ID: " . $user['id'] . "<br>";
        echo "Password hash length: " . strlen($user['password']) . "<br>";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>
