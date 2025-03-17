<?php
require_once "../functions/core.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Доступ запрещен");
}

if (isset($_GET['id'])) {
    $note_id = (int)$_GET['id'];
    try {
        if (deleteNote($note_id)) {
            header("Location: /index.php");
            exit();
        }
    } catch (Exception $e) {
        header("Location: /index.php?error=delete_failed&message=" . urlencode($e->getMessage()));
        exit();
    }
}

header("Location: /index.php?error=delete_failed");
exit();
