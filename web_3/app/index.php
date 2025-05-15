<?php
// require 'db.php'; // при возникновении ошибки в файле о ней сообщится
require_once 'db.php';

// include 'db.php'; обычное подключение файла
// include_once 'db.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавление блюда в меню</title>
</head>
<body>
    <h1>Добавить блюдо в меню</h1>

    <?php if (!empty($message)) : ?>
        <p><strong><?php echo htmlspecialchars($message); ?></strong></p>
    <?php endif; ?>

    <form id="menuForm" method="POST" action="submit.php">
        <label for="name">Название блюда:</label>
        <input type="text" name="dish_name" id="dish_name">
        <br>
        <label for="ingredient1">Первый ингредиент:</label>
        <input type="text" name="ingredient1" id="ingredient1">
        <br>
        <label for="ingredient2">Второй ингредиент:</label>
        <input type="text" name="ingredient2" id="ingredient2">
        <br>
        <label for="weight">Вес:</label>
        <input type="text" name="weight" id="weight">
        <br>
        <button type="submit">Добавить</button>
    </form>

    <h2>список сообщений</h2>
    <?php
        $stmt = $conn->query('SELECT * FROM menu');
        foreach ($stmt as $row) {
            echo '<p><strong>' . '&nbsp' . $row['dish_name'] . '&nbsp' . $row['ingredient1'] . '&nbsp' . $row['ingredient2'] . '&nbsp' . $row['weight'] . '</strong></p>';
        }

        // $stmt = $conn->prepare('select * from user where user_id = ? desc'); //POST метод
        // $stmt->execute([2]);
        // foreach ($stmt as $row) {
        //     echo '<p><strong>' . $row['name'] . '</strong></p>';
        // }
    ?>
<script src="val.js"></script>
</body>
</html>