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
    // Получаем данные из формы
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $calories = $_POST['calories'];
    $proteins = $_POST['proteins'];
    $fats = $_POST['fats'];
    $carbs = $_POST['carbs'];

    try {
        $pdo = Database::getInstance()->getConnection();

        // Обновляем данные в таблице
        $stmt = $pdo->prepare('UPDATE products SET name = ?, calories = ?, proteins = ?, fats = ?, carbs = ? WHERE id = ?');
        $stmt->execute([$name, $calories, $proteins, $fats, $carbs, $id]);

        header('Location: ../dashboard.php');
        exit;
    } catch (PDOException $e) {
        die('Ошибка базы данных: ' . $e->getMessage());
    }
}
