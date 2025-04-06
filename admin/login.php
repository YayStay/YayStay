<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
require '../db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            if ($password === $user['password']) { 
                $_SESSION['admin'] = true;
                $_SESSION['admin_username'] = $username;
                $_SESSION['last_access'] = time();
                header("Location: admin_panel.php");
                exit();
            } else {
                $error = 'Невалидно потребителско име или парола!';
            }
        } else {
            $error = 'Невалидно потребителско име или парола!';
        }
    } catch (PDOException $e) {
        $error = 'грешка ' . $e->getMessage();
    }
}

error_log("Current session status: " . print_r($_SESSION, true));
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Admin Girişi</title>
    <style>
        .container { 
            max-width: 400px; 
            margin: 50px auto; 
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .error { 
            color: red; 
            margin-bottom: 10px; 
        }
        .form-group {
            margin-bottom: 15px;
        }
        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Вход на администратор</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <input type="text" name="username" placeholder="Потребителско име" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="парола" required>
            </div>
            <button type="submit">Вход</button>
        </form>
    </div>
</body>
</html>
