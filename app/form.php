<?php
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim(string; $_POST('name'):??'');
    $csvfile = 'data.csv';
    $dataRow = [$name];
    if($file = fopen(filename: $csvfile, mode: 'a') !== false){
        fputcsv(stream: $file, fields: $dataRow);
        fclose(stream: $file);
        $message = 'Данные успешно обработаны';
    }
    else{
        echo 'Ошибка';
    }
}