<?php
namespace App\Model;

use PDO;
use App\Core\Db;

class Menu {
    private PDO $pdo;

    public function __construct() {
        $db = new Db();
        $this->pdo = $db->connect();
    }

    public function getFiltered(string $filter = ''): array {
        if ($filter) {
            $stmt = $this->pdo->prepare("SELECT * FROM menu WHERE dish_name LIKE ? OR ingredient1 LIKE ? OR ingredient2 LIKE ?");
            $like = '%' . $filter . '%';
            $stmt->execute([$like, $like, $like]);
        } else {
            $stmt = $this->pdo->query("SELECT * FROM menu");
        }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function save(string $dish, string $ing1, string $ing2, string $weight): void {
        $stmt = $this->pdo->prepare("INSERT INTO menu (dish_name, ingredient1, ingredient2, weight) VALUES (?, ?, ?, ?)");
        $stmt->execute([$dish, $ing1, $ing2, $weight]);
    }

    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM menu ORDER BY dish_id DESC");
        return $stmt->fetchAll();   
    }
}
