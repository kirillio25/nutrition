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
    $name = trim($_POST['name']);
    $calories = $_POST['calories'] ?? null;
    $proteins = $_POST['proteins'] ?? null;
    $fats = $_POST['fats'] ?? null;
    $carbs = $_POST['carbs'] ?? null;

    // Проверяем валидность данных
    if (empty($name) || $calories === null || $proteins === null || $fats === null || $carbs === null) {
        die('Некорректные данные. Проверьте форму.');
    }

    try {
        // Подключение к базе данных
        $pdo = Database::getInstance()->getConnection();

        // Вставляем данные в таблицу products
        $stmt = $pdo->prepare('INSERT INTO products (name, calories, proteins, fats, carbs) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$name, $calories, $proteins, $fats, $carbs]);

        // Перенаправляем обратно на dashboard.php
        header('Location: ../dashboard.php');
        exit;
    } catch (PDOException $e) {
        die('Ошибка базы данных: ' . $e->getMessage());
    }
} else {
    // Если не POST-запрос, перенаправляем обратно
    header('Location: ../dashboard.php');
    exit;
}

