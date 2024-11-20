<?php
session_start();
require_once '../config/bd.php';

use Config\Database;

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'] ?? null;
    $amount = $_POST['amount'] ?? null;

    if (empty($product_id) || empty($amount) || $amount <= 0) {
        die('Некорректные данные. Попробуйте снова.');
    }

    try {
        $pdo = Database::getInstance()->getConnection();

        $stmt = $pdo->prepare('INSERT INTO meal_plans (user_id, product_id, amount) VALUES (?, ?, ?)');
        $stmt->execute([$user_id, $product_id, $amount]);

        header('Location: ../dashboard.php');
        exit;
    } catch (PDOException $e) {
        die('Ошибка базы данных: ' . $e->getMessage());
    }
} else {
    header('Location: ../dashboard.php');
    exit;
}
?>
