<?php
require_once "{$_SERVER['DOCUMENT_ROOT']}/vendor/functions/core.php";

if (!isset($_GET['test_id'])) {
    header("Location: /test_list.php");
    exit();
}

$test_id = (int)$_GET['test_id'];

// Получаем информацию о тесте
$stmt = $link->prepare("SELECT * FROM tests WHERE id = ?");
$stmt->bind_param("i", $test_id);
$stmt->execute();
$test = $stmt->get_result()->fetch_assoc();

if (!$test) {
    header("Location: /test_list.php");
    exit();
}

// Получаем все вопросы теста
$stmt = $link->prepare("SELECT * FROM questions WHERE test_id = ? ORDER BY id ASC");
$stmt->bind_param("i", $test_id);
$stmt->execute();
$questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$total_questions = count($questions);

// Проверка наличия вопросов
if ($total_questions === 0) {
    echo "<div class='container'><div class='test-result'>";
    echo "<h2>Ошибка</h2>";
    echo "<p>В данном тесте пока нет вопросов.</p>";
    echo "<a href='test_list.php' class='button'>К списку тестов</a>";
    echo "</div></div>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['test_started'])) {
        $_SESSION['current_question'] = 0;
        $_SESSION['score'] = 0;
        $_SESSION['answers'] = [];
        $_SESSION['test_started'] = true;
    }

    if (isset($_POST['answer'])) {
        $current = $_SESSION['current_question'];
        $_SESSION['answers'][$current] = [
            'given' => $_POST['answer'],
            'correct' => $questions[$current]['correct_answer']
        ];

        if ($_POST['answer'] === $questions[$current]['correct_answer']) {
            $_SESSION['score']++;
        }
        $_SESSION['current_question']++;
    }
}

$current_question = isset($_SESSION['current_question']) ? $_SESSION['current_question'] : 0;
?>

<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($test['title']) ?></title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>

<body>
    <div class="container">
        <?php if ($current_question >= $total_questions): ?>
            <div class="test-result">
                <h2>Тест завершен!</h2>
                <div class="result-score">
                    <?php 
                    $score = isset($_SESSION['score']) ? $_SESSION['score'] : 0;
                    $percentage = $total_questions > 0 ? round(($score / $total_questions) * 100) : 0;
                    ?>
                    Результат: <?= $score ?> из <?= $total_questions ?>
                    (<?= $percentage ?>%)
                </div>

                <div class="result-details">
                    <?php foreach ($_SESSION['answers'] as $index => $answer): ?>
                        <div class="answer-review <?= $answer['given'] === $answer['correct'] ? 'correct' : 'incorrect' ?>">
                            <p>Вопрос <?= $index + 1 ?>:
                                <?= htmlspecialchars($questions[$index]['question']) ?></p>
                            <p>Ваш ответ: <?= htmlspecialchars($answer['given']) ?></p>
                            <?php if ($answer['given'] !== $answer['correct']): ?>
                                <p>Правильный ответ: <?= htmlspecialchars($answer['correct']) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php
                // Очистка сессии теста
                unset($_SESSION['test_started']);
                unset($_SESSION['current_question']);
                unset($_SESSION['score']);
                unset($_SESSION['answers']);
                ?>

                <a href="test.php?test_id=<?= $test_id ?>" class="button">Пройти тест заново</a>
                <a href="test_list.php" class="button">К списку тестов</a>
            </div>
        <?php else: ?>
            <div class="test-container">
                <div class="question-header">
                    <h2><?= htmlspecialchars($test['title']) ?></h2>
                    <span>Вопрос <?= $current_question + 1 ?> из <?= $total_questions ?></span>
                </div>

                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= $total_questions > 0 ? ($current_question / $total_questions) * 100 : 0 ?>%"></div>
                </div>

                <form method="post">
                    <div class="question">
                        <?= htmlspecialchars($questions[$current_question]['question']) ?>
                    </div>

                    <div class="options-list">
                        <?php
                        $options = json_decode($questions[$current_question]['options'], true);
                        foreach ($options as $option): ?>
                            <label class="option-label">
                                <input type="radio" name="answer" value="<?= htmlspecialchars($option) ?>" required>
                                <?= htmlspecialchars($option) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <button type="submit" class="button">
                        <?= $current_question == $total_questions - 1 ? 'Завершить тест' : 'Следующий вопрос' ?>
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>