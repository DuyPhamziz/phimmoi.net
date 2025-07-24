<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{

    private static $pdo = null;

    public static function connect()
    {
        if (self::$pdo === null) {
            $host = $_ENV['DB_HOST'];
            $db   = $_ENV['DB_NAME'];
            $user = $_ENV['DB_USER'];
            $pass = $_ENV['DB_PASS'];
            $port = $_ENV['DB_PORT'];
            $charset = 'utf8';

            $dsn = "pgsql:host=$host;port=$port;dbname=$db;options='--client_encoding=$charset'";
            try {
                self::$pdo = new PDO($dsn,$user,$pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                die("Kết nối thất bại: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}

?>