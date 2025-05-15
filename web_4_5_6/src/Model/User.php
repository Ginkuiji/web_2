<?php
namespace App\Model;

use PDO;
use App\Core\Db;

class User {
    private PDO $conn;

    public function __construct() {
        $this->conn = (new Db())->connect();
    }

    public function register(string $login, string $password, string $role = 'user'): bool {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("INSERT INTO users (user_role, user_login, user_password) VALUES (?, ?, ?)");
        return $stmt->execute([$role, $login, $hashedPassword]);
    }

    public function findByLogin(string $login): ?array {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE user_login = ?");
        $stmt->execute([$login]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function verifyPassword(string $password, string $hashedPassword): bool {
        return password_verify($password, $hashedPassword);
    }
}
?>