<?php

namespace App\Core;

use PDO;
use PDOException;

class Db {
    private static ?PDO $pdo = null;

    public static function connect(): PDO {
        if (self::$pdo === null) {
            $host = "db";
            $db = "mydb";
            $user = "user";
            $pass = "pass";
            $encode = "utf8";

            $dsn = "mysql:host=$host;dbname=$db;charset=$encode";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ];

            try {
                self::$pdo = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                die('Ошибка подключения: ' . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}