<?php
require_once "../components/header.php";

// Проверка прав администратора
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: /index.php');
    exit();
}

// Обработка формы создания опроса
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = trim($_POST['question']);
    $options = array_filter($_POST['options']); // Удаляем пустые варианты

    if (!empty($question) && count($options) >= 2) {
        if (createPoll($question, $options)) {
            header("Location: /vendor/components/polls.php");
            exit();
        } else {
            $error = "Ошибка при создании опроса";
        }
    } else {
        $error = "Необходимо указать вопрос и минимум 2 варианта ответа";
    }
}
?>

<h1>Управление опросами</h1>

<?php if (isset($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="poll-form">
    <h2>Создать новый опрос</h2>
    <form method="post">
        <label>
            Вопрос:
            <input type="text" name="question" required class="poll-input">
        </label>

        <div id="optionsContainer">
            <label>Варианты ответов:</label>
            <div class="option-inputs">
                <input type="text" name="options[]" required class="poll-input">
                <input type="text" name="options[]" required class="poll-input">
            </div>
        </div>

        <button type="button" id="addOption" class="button">Добавить вариант</button>
        <button type="submit" class="button">Создать опрос</button>
    </form>
</div>

<script>
document.getElementById('addOption').addEventListener('click', function() {
    const container = document.querySelector('.option-inputs');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'options[]';
    input.className = 'poll-input';
    input.required = true;
    container.appendChild(input);
});
</script>

<?php require "../components/footer.php"; ?>
