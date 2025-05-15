<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $fields = [
        'dish_name' => 'Название блюда',
        'ingredient1' => 'Первый ингредиент',
        'ingredient2' => 'Второй ингредиент'
    ];

    $data = [];
    $errors = [];

    foreach ($fields as $field => $label) {
        $value = htmlspecialchars(trim($_POST[$field] ?? ''));
        $data[$field] = $value;

        if (empty($value) || !preg_match('/^[а-яА-ЯёЁa-zA-Z\s]+$/u', $value)) {
            $errors[] = "Введите корректное значение для поля: $label.";
        }
    }


    $weight = htmlspecialchars(trim($_POST['weight'] ?? ''));
    if (!ctype_digit($weight) || (int)$weight < 1 || (int)$weight > 5000) {
        $errors[] = "Введите допустимый вес блюда (от 1 до 5000 грамм).";
    } else {
        $data['weight'] = (int)$weight;
    }

    if (!empty($dish_name)){
        $stmt = $conn->prepare('INSERT INTO menu (dish_id, dish_name, ingredient1, ingredient2, weight) VALUES (DEFAULT, ?, ?, ?, ?)');
        $result = $stmt->execute([$data['dish_name'], $data['ingredient1'], $data['ingredient2'], $data['weight']]);
        $message = "Запись добавлена.";
        echo "<script>alert('" . $message . "'); window.location.href='index.php';</script>";
    }
}

?>