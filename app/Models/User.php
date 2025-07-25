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
}

