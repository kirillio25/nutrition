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
    $amount = $_POST['amount'];

    try {
        $pdo = Database::getInstance()->getConnection();

        // Обновляем данные в таблице meal_plans
        $stmt = $pdo->prepare('UPDATE meal_plans SET amount = ? WHERE id = ?');
        $stmt->execute([$amount, $id]);

        header('Location: ../dashboard.php');
        exit;
    } catch (PDOException $e) {
        die('Ошибка базы данных: ' . $e->getMessage());
    }
}
