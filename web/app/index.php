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
        <p><strong><?php echo $message; ?></strong></p>
    <?php endif; ?>

    <form method="POST" action="form.php">
        <label for="dish_name">Название блюда:</label>
        <input type="text" name="dish_name">
        <br>
        <label for="ingredient1">Первый ингредиент:</label>
        <input type="text" name="ingredient1">
        <br>
        <label for="ingredient2">Второй ингредиент:</label>
        <input type="text" name="ingredient2">
        <br>
        <label for="weight">Масса:</label>
        <input type="text" name="dish_weight">
        <br>
        <button type="submit">Добавить</button>
    </form>
</body>
</html>