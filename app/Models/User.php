<?php 

namespace App\Models;
use App\Core\Database;
use App\Utils\UsernameGenerator;
use PDO;        
class User {
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }
    public function findByGoogleId(string $googleid): ?array {
       
        $stmt = $this->db->prepare("SELECT * FROM users WHERE google_id = ?");
        $stmt->execute([
            $googleid
        ]);
        $user = $stmt->fetch();
        return $user ?: null;
    }
    public function find($id)
    {

        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([
            $id
        ]);
        $user = $stmt->fetch();
        return $user ?: null;
    }
    public function create(array $data): ?array {

        $randomUser = UsernameGenerator::generate();
        $name = mb_strlen($data['name']) > 8 ? $randomUser['name'] : $data['name'];
        $icon = $randomUser['icon'];

        $stmt= $this->db->prepare("
        INSERT INTO users (google_id, name, email, avatar, icon, role)
            VALUES (:google_id, :name, :email, :avatar, :icon, :role);
         ");

         $stmt->execute([
            'google_id' => $data['id'],
            'name' => $name,
            'email' => $data['email'],
            'avatar' => $data['avatar'],
            'icon' => $icon,
            'role' => $data['role'] ?? 'user'  
        ]);
         return $this->findByGoogleId($data['id']);
    }
    public function getAll() {
        $sql = "SELECT id, avatar, name, email, role
                FROM users";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function setAdmin($userId)
    {
        $sql = "UPDATE users SET role = 'admin' WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $userId]);
    }

    // Hủy quyền admin
    public function revokeAdmin($userId)
    {
        $sql = "UPDATE users SET role = 'user' WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $userId]);
    }

}

