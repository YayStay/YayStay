<?php
require 'db.php';

if (!isset($_GET['house_id']) || !is_numeric($_GET['house_id'])) {
    die('Липсва или е невалидно ID на къщата!');
}

$house_id = $_GET['house_id'];

$stmt = $pdo->prepare("SELECT name, location, price FROM houses WHERE id = :house_id");
$stmt->execute(['house_id' => $house_id]);
$house = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$house) {
    die('Къщата не е намерена!');
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($house['name']); ?></title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 600px; margin: auto; }
        h1 {color: hotpink; }
        .back-link { margin-bottom: 15px; display: inline-block; }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-link">⬅ Назад към всички къщи</a>
        <h1><?php echo htmlspecialchars($house['name']); ?></h1>
        <p><strong>Локация:</strong> <?php echo htmlspecialchars($house['location']); ?></p>
        <p><strong>Цена на вечер:</strong> <?php echo htmlspecialchars($house['price']); ?> лв</p>

        <h2>Направи резервация</h2>
        <form action="make_reservation.php" method="post">
            <input type="hidden" name="house_id" value="<?php echo htmlspecialchars($house_id); ?>">
            <label for="checkin">Дата на настаняване:</label>
            <input type="date" id="checkin" name="checkin" required>
            
            <label for="checkout">Дата на напускане:</label>
            <input type="date" id="checkout" name="checkout" required>

            <label for="guest_name">Вашето име:</label>
            <input type="text" id="guest_name" name="guest_name" required>

            <label for="guest_email">Вашият имейл:</label>
            <input type="email" id="guest_email" name="guest_email" required>

            <button type="submit">Резервирай</button>
        </form>
    </div>
</body>
</html>
