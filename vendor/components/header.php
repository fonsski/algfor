<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/vendor/functions/core.php";
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форум для обучения алгебре</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <header class="header">
        <nav class="nav">
            <a href="/index.php">Главная</a>
            <a href="/vendor/components/test_list.php">Тесты</a>
            <a href="/vendor/components/graphics.php">Графика</a>
            <a href="/vendor/components/guestbook.php">Гостевая книга</a>
            <a href="/vendor/components/polls.php">Опросы</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="/vendor/admin/add_note.php">Добавить заметку</a>
                    <a href="/vendor/admin/manage_polls.php">Управление опросами</a>
                <?php endif; ?>
                <a href="/?logout">Выйти (<?= htmlspecialchars($_SESSION['user_name']) ?>)</a>
            <?php else: ?>
                <a href="/vendor/components/login.php">Войти</a>
                <a href="/vendor/components/register.php">Регистрация</a>
            <?php endif; ?>
        </nav>
    </header>
    <div class="container">