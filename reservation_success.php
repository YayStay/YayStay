<?php
session_start();
require 'db.php';

if (!isset($_SESSION['reservation_success']) || !isset($_GET['id'])) {
    header('Location: room.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT r.*, h.name as house_name, h.location 
    FROM reservations r 
    JOIN houses h ON r.house_id = h.id 
    WHERE r.id = ?
");
$stmt->execute([$_GET['id']]);
$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

unset($_SESSION['reservation_success']);
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Успешна резервация</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container mt-5">
        <div class="alert alert-success">
            <h4>Резервацията е успешна!</h4>
            <p>Благодарим ви за резервацията. Детайли за вашата резервация:</p>
            <ul>
                <li>Къща: <?= htmlspecialchars($reservation['house_name']) ?></li>
                <li>Дати: <?= htmlspecialchars($reservation['checkin_date']) ?> - <?= htmlspecialchars($reservation['checkout_date']) ?></li>
                <li>Гости: <?= htmlspecialchars($reservation['num_guests']) ?></li>
                <li>Обща сума: <?= number_format($reservation['total_price'], 2) ?> лв.</li>
            </ul>
            <?php if ($reservation['payment_method'] === 'bank'): ?>
            <div class="bank-details">
                <h5>Банкови детайли за плащане:</h5>
                <p>Банка: YourBank</p>
                <p>IBAN: BG00 XXXX XXXX XXXX XXXX</p>
                <p>Основание: Резервация №<?= $reservation['id'] ?></p>
            </div>
            <?php endif; ?>
            <hr>
            <p>Ще получите потвърждение на имейл адреса си.</p>
            <a href="room.php" class="btn btn-primary">Обратно към къщите</a>
        </div>
    </div>
</body>
</html>
