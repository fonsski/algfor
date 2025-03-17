<?php
require_once "../functions/core.php";

// Проверка прав администратора
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: /index.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: /index.php');
    exit();
}

$id = (int)$_GET['id'];
$stmt = $link->prepare("SELECT * FROM notes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$note = $stmt->get_result()->fetch_assoc();

if (!$note) {
    header('Location: /index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    
    if (updateNote($id, $title, $content)) {
        header('Location: /index.php');
        exit();
    } else {
        $error = "Ошибка при обновлении заметки";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Редактировать заметку</title>
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
    <h1>Редактировать заметку</h1>
    <form method="POST">
        <input type="text" name="title" placeholder="Заголовок" value="<?php echo htmlspecialchars($note['title']); ?>" required>
        <textarea name="content" placeholder="Содержание" rows="10" required><?php echo htmlspecialchars($note['content']); ?></textarea>
        <input type="submit" value="Сохранить изменения">
    </form>
    <p><a href="index.php">Вернуться к списку заметок</a></p>
</body>
</html>
