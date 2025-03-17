<?php
require_once "../components/header.php";

// Проверка прав администратора
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: /index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    
    if (addNote($title, $content, $_SESSION['user_id'])) {
        header('Location: /index.php');
        exit();
    } else {
        $error = "Ошибка при добавлении заметки";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Добавить заметку</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 0 20px;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        form {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        input[type="submit"] {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <h1>Добавить заметку</h1>
    
    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form method="post">
        <label for="title">Заголовок:</label>
        <input type="text" id="title" name="title" required>
        
        <label for="content">Содержание:</label>
        <textarea id="content" name="content" rows="4" required></textarea>
        
        <input type="submit" value="Добавить">
    </form>
    <p><a href="/index.php">Вернуться к списку заметок</a></p>
</body>
</html>
<?php require "../components/footer.php"; ?>
