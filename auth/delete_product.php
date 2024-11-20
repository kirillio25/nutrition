<?php
session_start();
require_once '../config/bd.php';

use Config\Database;

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    try {
        $pdo = Database::getInstance()->getConnection();

        // Удаляем продукт
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id]);

        header('Location: ../dashboard.php');
        exit;
    } catch (PDOException $e) {
        die('Ошибка базы данных: ' . $e->getMessage());
    }
}
