<?php

namespace Config;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private ?PDO $connection = null;

    private string $host = 'localhost';
    private string $dbname = 'nutrition';

    private string $port = '3307';
    private string $username = 'root';
    private string $password = 'root';

    // Закрываем конструктор, чтобы исключить создание объекта через new
    private function __construct()
    {
        try {
            $this->connection = new PDO(
                "mysql:host={$this->host};port={$this->port}dbname={$this->dbname};charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Ошибка подключения к базе данных: " . $e->getMessage());
        }
    }

    // Запрещаем клонирование
    private function __clone()
    {
    }


    // Метод для получения единственного экземпляра класса
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Возвращаем объект PDO
    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
