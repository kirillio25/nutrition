<?php
require_once '../config/bd.php';

use Config\Database;

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    try {
        $pdo = Database::getInstance()->getConnection();

        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Успешный вход: сохраняем данные в сессию
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            // Перенаправляем на dashboard.php
            header('Location: ../dashboard.php');
            exit;
        } else {
            echo 'Неверный email или пароль.';
        }
    } catch (PDOException $e) {
        die('Ошибка: ' . $e->getMessage());
    }
}

