<?php
require 'db.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $checkin = $_POST['checkin'];
        $checkout = $_POST['checkout'];

        $stmt = $pdo->prepare("
            SELECT h.id, h.name, h.location, h.type 
            FROM houses h 
            WHERE h.id NOT IN (
                SELECT DISTINCT r.house_id 
                FROM reservations r 
                WHERE (r.checkin_date <= :checkout AND r.checkout_date >= :checkin)
            )
        ");

        $stmt->execute([
            'checkin' => $checkin,
            'checkout' => $checkout
        ]);

        $available_houses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($available_houses);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
