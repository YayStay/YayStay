<?php
require 'db.php';

$stmt = $pdo->query('SELECT id, name, location FROM houses'); 
$houses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Къщи за настаняване</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 800px; margin: auto; }
        .house { border: 1px solid #ddd; padding: 15px; margin-bottom: 10px; border-radius: 5px; }
        .house h2 { margin: 0; }
        .house a { text-decoration: none; color:rgb(255, 0, 191); font-weight: bold; }
        .house a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Налични къщи</h1>
        <?php foreach ($houses as $house): ?>
            <div class="house">
                <h2><?php echo htmlspecialchars($house['name']); ?></h2>
                <p><strong>Локация:</strong> <?php echo htmlspecialchars($house['location']); ?></p>
                
                <a href="house.php?house_id=<?php echo $house['id']; ?>">Виж детайли &rarr;</a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
