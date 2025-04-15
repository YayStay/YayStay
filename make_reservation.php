<?php
require 'db.php';

// Get JSON data from request
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validate input
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid data format']);
    exit;
}

try {
    // Check if dates are available
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM reservations 
        WHERE house_id = ? 
        AND ((checkin_date BETWEEN ? AND ?) 
        OR (checkout_date BETWEEN ? AND ?)
        OR (? BETWEEN checkin_date AND checkout_date))
    ");
    
    $stmt->execute([
        $data['house_id'],
        $data['checkin'],
        $data['checkout'],
        $data['checkin'],
        $data['checkout'],
        $data['checkin']
    ]);
    
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'Избраните дати не са свободни']);
        exit;
    }

    // Calculate total price
    $stmt = $pdo->prepare("SELECT winter_price, summer_price FROM houses WHERE id = ?");
    $stmt->execute([$data['house_id']]);
    $house = $stmt->fetch(PDO::FETCH_ASSOC);

    $checkin_date = new DateTime($data['checkin']);
    $checkout_date = new DateTime($data['checkout']);
    $nights = $checkout_date->diff($checkin_date)->days;
    
    // Determine season price
    $month = (int)$checkin_date->format('m');
    $price_per_night = ($month >= 6 && $month <= 8) ? $house['summer_price'] : $house['winter_price'];
    $total_price = $price_per_night * $nights;

    // Insert reservation with payment method
    $stmt = $pdo->prepare("
        INSERT INTO reservations (
            house_id, 
            checkin_date, 
            checkout_date, 
            guest_name, 
            guest_email, 
            guest_phone, 
            num_guests, 
            payment_method,
            total_price
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $data['house_id'],
        $data['checkin'],
        $data['checkout'],
        $data['guest_name'],
        $data['guest_email'],
        $data['guest_phone'],
        $data['guests'],
        $data['payment_method'],
        $total_price
    ]);

    // Prepare response message based on payment method
    $message = 'Резервацията е успешна! ';
    switch($data['payment_method']) {
        case 'bank_transfer':
            $message .= 'Моля, направете банков превод в рамките на 24 часа.';
            break;
        case 'card':
            $message .= 'Ще бъдете пренасочени към страницата за плащане.';
            break;
        case 'cash':
            $message .= 'Моля, подгответе сумата в брой при настаняване.';
            break;
    }

    echo json_encode([
        'success' => true,
        'message' => $message,
        'reservation_id' => $pdo->lastInsertId(),
        'payment_method' => $data['payment_method']
    ]);

} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Възникна грешка при обработката на резервацията.'
    ]);
}
?>