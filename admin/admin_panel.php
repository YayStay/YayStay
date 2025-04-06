<?php
session_start();
require '../db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $id = $_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM houses WHERE id = ?");
    $stmt->execute([$id]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    try {
        $pdo->beginTransaction();
        
        $image_url = 'images/default.jpg';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $image_url = uploadImage($_FILES['image']);
        }
        
        $image1_url = null;
        $image2_url = null;
        $image3_url = null;
        
        if (isset($_FILES['image1']) && $_FILES['image1']['error'] === 0) {
            $image1_url = uploadImage($_FILES['image1']);
        }
        if (isset($_FILES['image2']) && $_FILES['image2']['error'] === 0) {
            $image2_url = uploadImage($_FILES['image2']);
        }
        if (isset($_FILES['image3']) && $_FILES['image3']['error'] === 0) {
            $image3_url = uploadImage($_FILES['image3']);
        }

        $stmt = $pdo->prepare("INSERT INTO houses (name, location, type, image_url, image1_url, image2_url, image3_url, winter_price, summer_price, max_guests) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['location'],
            $_POST['type'],
            $image_url,
            $image1_url,
            $image2_url,
            $image3_url,
            $_POST['winter_price'],
            $_POST['summer_price'],
            $_POST['max_guests']
        ]);
        
        $house_id = $pdo->lastInsertId();
        
        if (isset($_POST['amenities']) && is_array($_POST['amenities'])) {
            $stmt = $pdo->prepare("INSERT INTO house_amenities (house_id, amenity_id) VALUES (?, ?)");
            foreach ($_POST['amenities'] as $amenity_id) {
                if (!empty($amenity_id)) { 
                    $stmt->execute([$house_id, $amenity_id]);
                }
            }
        }
        
        $pdo->commit();
        $_SESSION['success_message'] = "Къщата е добавена успешно!";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error_message'] = "грешка: " . $e->getMessage();
        error_log("Database error: " . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    try {
        $pdo->beginTransaction();
        
        $update_fields = [
            'name' => $_POST['edit_name'],
            'location' => $_POST['edit_location'],
            'type' => $_POST['edit_type'],
            'winter_price' => $_POST['edit_winter_price'],
            'summer_price' => $_POST['edit_summer_price'],
            'max_guests' => $_POST['edit_max_guests']
        ];
        
        if (isset($_FILES['edit_image']) && $_FILES['edit_image']['error'] === 0) {
            $update_fields['image_url'] = uploadImage($_FILES['edit_image']);
        }
        
        if (isset($_FILES['edit_image1']) && $_FILES['edit_image1']['error'] === 0) {
            $update_fields['image1_url'] = uploadImage($_FILES['edit_image1']);
        }
        if (isset($_FILES['edit_image2']) && $_FILES['edit_image2']['error'] === 0) {
            $update_fields['image2_url'] = uploadImage($_FILES['edit_image2']);
        }
        if (isset($_FILES['edit_image3']) && $_FILES['edit_image3']['error'] === 0) {
            $update_fields['image3_url'] = uploadImage($_FILES['edit_image3']);
        }
        
        $sql = "UPDATE houses SET " . implode(', ', array_map(function($key) {
            return "$key = :$key";
        }, array_keys($update_fields))) . " WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($update_fields + ['id' => $_POST['edit_id']]);
        
        if (isset($_POST['edit_amenities'])) {
            $stmt = $pdo->prepare("DELETE FROM house_amenities WHERE house_id = ?");
            $stmt->execute([$_POST['edit_id']]);

            $stmt = $pdo->prepare("INSERT INTO house_amenities (house_id, amenity_id) VALUES (?, ?)");
            foreach ($_POST['edit_amenities'] as $amenity_id) {
                $stmt->execute([$_POST['edit_id'], $amenity_id]);
            }
        }

        $pdo->commit();
        $_SESSION['success_message'] = "Къщата е успешно обновена!";
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error_message'] = "грешка: " . $e->getMessage();
    }
}

function uploadImage($file) {
    $target_dir = "../images/"; 
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $file_name = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $file_name;
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return 'images/' . $file_name; 
    }
    return null;
}

$amenities = $pdo->query("SELECT * FROM amenities")->fetchAll(PDO::FETCH_ASSOC);

$houses = $pdo->query("
    SELECT h.*, GROUP_CONCAT(a.name) as amenities 
    FROM houses h 
    LEFT JOIN house_amenities ha ON h.id = ha.house_id 
    LEFT JOIN amenities a ON ha.amenity_id = a.id 
    GROUP BY h.id
")->fetchAll(PDO::FETCH_ASSOC);

$reservations = $pdo->query("
    SELECT r.*, h.name as house_name 
    FROM reservations r
    JOIN houses h ON r.house_id = h.id
    ORDER BY r.checkin_date DESC
")->fetchAll(PDO::FETCH_ASSOC);

$messages = $pdo->query("
    SELECT * FROM messages 
    ORDER BY created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_reservation'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
        $stmt->execute([$_POST['delete_reservation']]);
        $_SESSION['success_message'] = "Резервацията е изтрита успешно.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } catch(Exception $e) {
        $_SESSION['error_message'] = "Възникна грешка при изтриването на резервацията.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_message'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
        $stmt->execute([$_POST['delete_message']]);
        $_SESSION['success_message'] = "Съобщението е изтрито успешно.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } catch(Exception $e) {
        $_SESSION['error_message'] = "Възникна грешка при изтриването на съобщението.";
    }
}

$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../css/admin-panel.css">
    <style>
        #editForm {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.15);
            z-index: 1000;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }

        .close-button {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Houses Management</h1>
        
        <?php if ($success_message): ?>
            <div class="message success"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="message error"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <form class="admin-form" method="POST" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="House Name" required>
            <input type="text" name="location" placeholder="Location" required>
            <input type="text" name="type" placeholder="Type" required>
            
            <div class="form-group">
                <label>Допълнителни снимки:</label>
                <input type="file" name="image1" accept="image/*">
                <input type="file" name="image2" accept="image/*">
                <input type="file" name="image3" accept="image/*">
            </div>
            
            <div class="form-group">
                <label>Цена - зимен сезон:</label>
                <input type="number" name="winter_price" step="0.01" required>
            </div>
            
            <div class="form-group">
                <label>Цена - летен сезон:</label>
                <input type="number" name="summer_price" step="0.01" required>
            </div>
            
            <div class="form-group">
                <label>Максимален брой гости:</label>
                <input type="number" name="max_guests" required>
            </div>
            
            <h3>Удобства:</h3>
            <div class="amenities-grid">
                <?php foreach ($amenities as $amenity): ?>
                <div class="amenity-item">
                    <input type="checkbox" name="amenities[]" value="<?= $amenity['id'] ?>" id="amenity_<?= $amenity['id'] ?>">
                    <label for="amenity_<?= $amenity['id'] ?>"><?= htmlspecialchars($amenity['name']) ?></label>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="form-group">
                <label>Снимка:</label>
                <input type="file" name="image" accept="image/*">
            </div>
            
            <button type="submit" name="add">Добавяне на къща</button>
        </form>

        <table class="admin-table">
            <tr>
            <th>ID</th>
                <th>Име</th>
                <th>Местоположение</th>
                <th>Тип</th>
                <th>Зимна цена</th>
                <th>Лятна цена</th>
                <th>Максимален брой гости</th>
                <th>Удобства</th>
                <th>Действия</th>
            </tr>
            <?php foreach ($houses as $house): ?>
            <tr>
                <td><?= htmlspecialchars($house['id']) ?></td>
                <td><?= htmlspecialchars($house['name']) ?></td>
                <td><?= htmlspecialchars($house['location']) ?></td>
                <td><?= htmlspecialchars($house['type']) ?></td>
                <td><?= htmlspecialchars($house['winter_price']) ?></td>
                <td><?= htmlspecialchars($house['summer_price']) ?></td>
                <td><?= htmlspecialchars($house['max_guests']) ?></td>
                <td><?= htmlspecialchars($house['amenities'] ?? '') ?></td>
                <td>
                    <button onclick="showEditForm(<?= htmlspecialchars(json_encode($house)) ?>)">Редактиране</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="delete" value="1">
                        <input type="hidden" name="id" value="<?= $house['id'] ?>">
                        <button type="submit" onclick="return confirm('сигурен ли си')">Изтриване</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <div class="overlay" id="overlay"></div>
        <div id="editForm" class="edit-form">
            <button type="button" class="close-button" onclick="hideEditForm()">&times;</button>
            <h3>Редактиране на дома</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="update" value="1">
                <input type="hidden" name="edit_id" id="edit_id">
                
                <div class="form-group">
                    <label>Начална снимка:</label>
                    <input type="file" name="edit_image" accept="image/*">
                    <img id="preview_image" src="" alt="" style="max-width: 200px; display: none;">
                </div>
                
                <div class="form-group">
                    <label>Име:</label>
                    <input type="text" name="edit_name" id="edit_name" required>
                </div>
                
                <div class="form-group">
                    <label>местоположение:</label>
                    <input type="text" name="edit_location" id="edit_location" required>
                </div>
                
                <div class="form-group">
                    <label>Тип:</label>
                    <input type="text" name="edit_type" id="edit_type" required>
                </div>
                
                <div class="form-group">
                    <label>Цена за зимен сезон:</label>
                    <input type="number" name="edit_winter_price" id="edit_winter_price" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label>Цена за летен сезон:</label>
                    <input type="number" name="edit_summer_price" id="edit_summer_price" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label>Максимален брой гости:</label>
                    <input type="number" name="edit_max_guests" id="edit_max_guests" required>
                </div>
                
                <div class="form-group">
                    <label>Характеристики:</label>
                    <div class="amenities-grid">
                        <?php foreach ($amenities as $amenity): ?>
                        <div class="amenity-item">
                            <input type="checkbox" name="edit_amenities[]" value="<?= $amenity['id'] ?>" 
                                   id="edit_amenity_<?= $amenity['id'] ?>">
                            <label for="edit_amenity_<?= $amenity['id'] ?>"><?= htmlspecialchars($amenity['name']) ?></label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Снимка 1:</label>
                    <input type="file" name="edit_image1" accept="image/*">
                    <img id="preview_image1" src="" alt="" style="max-width: 200px; display: none;">
                </div>
                
                <div class="form-group">
                    <label>Снимка 2:</label>
                    <input type="file" name="edit_image2" accept="image/*">
                    <img id="preview_image2" src="" alt="" style="max-width: 200px; display: none;">
                </div>
                
                <div class="form-group">
                    <label>Снимка 3:</label>
                    <input type="file" name="edit_image3" accept="image/*">
                    <img id="preview_image3" src="" alt="" style="max-width: 200px; display: none;">
                </div>
                
                <button type="submit">Актуализация</button>
                <button type="button" onclick="hideEditForm()">Отказ</button>
            </form>
        </div>

        <h2 style="margin-top: 30px;">Резервации</h2>
        <table class="admin-table" style="width: 100%; margin-bottom: 30px;">
            <tr>
                <th>ID</th>
                <th>Къща</th>
                <th>Гост</th>
                <th>Email</th>
                <th>Телефон</th>
                <th>Настаняване</th>
                <th>Напускане</th>
                <th>Брой гости</th>
                <th>Цена</th>
                <th>Действия</th>
            </tr>
            <?php foreach ($reservations as $reservation): ?>
            <tr>
                <td><?= htmlspecialchars($reservation['id']) ?></td>
                <td><?= htmlspecialchars($reservation['house_name']) ?></td>
                <td><?= htmlspecialchars($reservation['guest_name']) ?></td>
                <td><?= htmlspecialchars($reservation['guest_email']) ?></td>
                <td><?= htmlspecialchars($reservation['guest_phone']) ?></td>
                <td><?= htmlspecialchars($reservation['checkin_date']) ?></td>
                <td><?= htmlspecialchars($reservation['checkout_date']) ?></td>
                <td><?= htmlspecialchars($reservation['num_guests']) ?></td>
                <td><?= number_format($reservation['total_price'], 2) ?> лв.</td>
                <td>
                    <form method="POST" onsubmit="return confirm('Сигурни ли сте, че искате да изтриете тази резервация?');">
                        <input type="hidden" name="delete_reservation" value="<?= $reservation['id'] ?>">
                        <button type="submit" style="background: #dc3545; color: white;">Изтрий</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <h2 style="margin-top: 30px;">Съобщения</h2>
        <table class="admin-table" style="width: 100%; margin-bottom: 30px;">
            <tr>
                <th>ID</th>
                <th>Име</th>
                <th>Имейл</th>
                <th>Телефон</th>
                <th>Съобщение</th>
                <th>Дата</th>
                <th>Действия</th>
            </tr>
            <?php foreach ($messages as $message): ?>
            <tr>
                <td><?= htmlspecialchars($message['id']) ?></td>
                <td><?= htmlspecialchars($message['name']) ?></td>
                <td><?= htmlspecialchars($message['email']) ?></td>
                <td><?= htmlspecialchars($message['phone']) ?></td>
                <td><?= htmlspecialchars($message['message']) ?></td>
                <td><?= htmlspecialchars($message['created_at']) ?></td>
                <td>
                    <form method="POST" onsubmit="return confirm('Сигурни ли сте, че искате да изтриете това съобщение?');">
                        <input type="hidden" name="delete_message" value="<?= $message['id'] ?>">
                        <button type="submit" style="background: #dc3545; color: white;">Изтрий</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <script>
        function showEditForm(house) {
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('editForm').style.display = 'block';
            document.getElementById('edit_id').value = house.id;
            document.getElementById('edit_name').value = house.name;
            document.getElementById('edit_location').value = house.location;
            document.getElementById('edit_type').value = house.type;
            document.getElementById('edit_winter_price').value = house.winter_price;
            document.getElementById('edit_summer_price').value = house.summer_price;
            document.getElementById('edit_max_guests').value = house.max_guests;
            
            document.querySelectorAll('input[name="edit_amenities[]"]').forEach(checkbox => {
                checkbox.checked = false;
            });
            
            if (house.amenities) {
                const amenityIds = house.amenity_ids ? house.amenity_ids.split(',') : [];
                amenityIds.forEach(id => {
                    const checkbox = document.getElementById('edit_amenity_' + id);
                    if (checkbox) checkbox.checked = true;
                });
            }
            
            const preview = document.getElementById('preview_image');
            if (house.image_url) {
                preview.src = '../' + house.image_url;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
            
            ['image1_url', 'image2_url', 'image3_url'].forEach((field, index) => {
                const preview = document.getElementById(`preview_image${index + 1}`);
                if (house[field]) {
                    preview.src = '../' + house[field];
                    preview.style.display = 'block';
                } else {
                    preview.style.display = 'none';
                }
            });
        }

        function hideEditForm() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('editForm').style.display = 'none';
        }
        </script>
    </div>
</body>
</html>
