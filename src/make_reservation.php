<?php
require 'db.php';
session_start();
header('Content-Type: application/json; charset=utf-8');

if (isset($_SESSION['last_reservation_time'])) {
    $timeDiff = time() - $_SESSION['last_reservation_time'];
    if ($timeDiff < 5) { 
        http_response_code(429); 
        echo json_encode([
            'status' => 'error',
            'message' => 'Моля, изчакайте няколко секунди преди да направите нова резервация'
        ]);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("
            SELECT r.*, DATE_FORMAT(r.checkin_date, '%d/%m/%Y') as formatted_checkin,
            DATE_FORMAT(r.checkout_date, '%d/%m/%Y') as formatted_checkout
            FROM reservations r
            WHERE house_id = :house_id 
            AND (
                (checkin_date <= :checkout AND checkout_date >= :checkin)
            )
        ");
        
        $stmt->execute([
            'house_id' => $_POST['house_id'],
            'checkin' => $_POST['checkin'],
            'checkout' => $_POST['checkout']
        ]);

        $existingReservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($existingReservations) > 0) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Къщата е заета за избраните дати',
                'busyDates' => array_map(function($res) {
                    return [
                        'start' => $res['formatted_checkin'],
                        'end' => $res['formatted_checkout']
                    ];
                }, $existingReservations)
            ]);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO reservations (house_id, checkin_date, checkout_date, guest_name, guest_email, guest_phone, num_guests, payment_method, total_price) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $_POST['house_id'],
            $_POST['checkin'],
            $_POST['checkout'],
            $_POST['guest_name'],
            $_POST['guest_email'],
            $_POST['guest_phone'],
            $_POST['guests'],
            $_POST['payment_method'] ?? 'cash',
            (float)$_POST['total_price'] 
        ]);

        $_SESSION['last_reservation_time'] = time();
        $_SESSION['reservation_success'] = true;
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Резервацията е успешна!',
            'reservation_id' => $pdo->lastInsertId()
        ]);

    } catch (Exception $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Възникна грешка при обработката на резервацията'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Невалиден метод на заявка'
    ]);
}
?>