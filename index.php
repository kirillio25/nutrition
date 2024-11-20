<?php
session_start();

// Проверяем, авторизован ли пользователь
if (isset($_SESSION['user_id'])) {
    // Если пользователь авторизован, перенаправляем на dashboard.php
    header('Location: dashboard.php');
    exit;
} else {
    // Если не авторизован, перенаправляем на login.php
    header('Location: login.html');
    exit;
}
