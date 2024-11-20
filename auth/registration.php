<?php
require_once '../config/bd.php';

use Config\Database;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);

    if ($password !== $confirmPassword) {
        die('Пароли не совпадают.');
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    try {
        $pdo = Database::getInstance()->getConnection();

        // Проверка на существование email
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            die('Пользователь с таким email уже существует.');
        }

        // Сохранение пользователя
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, $hashedPassword]);

        // Перенаправление на страницу авторизации с успешным сообщением
        header('Location: ../login.html?success=1');
        exit;
    } catch (PDOException $e) {
        die('Ошибка: ' . $e->getMessage());
    }
}

