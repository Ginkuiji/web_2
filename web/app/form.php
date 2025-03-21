<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dish_name = trim($_POST['dish_name'] ?? '');
    $ingredient1 = trim($_POST['ingredient1'] ?? '');
    $ingredient2 = trim($_POST['ingredient2'] ?? '');
    $dish_weight = trim($_POST['dish_weight'] ?? '');

    $csvfile = 'menu.csv';
    $dataRow = [$dish_name, $ingredient1, $ingredient2, $dish_weight];

    if (($file = fopen($csvfile, 'a')) !== false) {
        fputcsv($file, $dataRow);
        fclose($file);
        $message = 'Блюдо успешно добавлено в меню!';
        echo $message;
    } else {
        $message = 'Ошибка при записи данных!';
        echo $message;
    }
}
?>