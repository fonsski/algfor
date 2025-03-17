<?php
// Подключаем шапку сайта
require_once "header.php";

// Функционал отправки сообщения в гостевую книгу
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $content = trim($_POST['content']);
    $formatted_content = nl2br(htmlspecialchars($content));

    // Форматирование текста
    $formatted_content = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $formatted_content);
    $formatted_content = preg_replace('/\n- (.*)/s', '<li>$1</li>', $formatted_content);
    $formatted_content = preg_replace('/\[(.*?)\]\((.*?)\)/s', '<a href="$2">$1</a>', $formatted_content);

    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ? $_POST['email'] : null;
    $website = filter_var($_POST['website'], FILTER_VALIDATE_URL) ? $_POST['website'] : null;

    $stmt = $link->prepare("INSERT INTO guestbook (user_id, content, formatted_content, email, website) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $_SESSION['user_id'], $content, $formatted_content, $email, $website);
    $stmt->execute();

    header("Location: guestbook.php");
    exit();
}

// Получение записей
$entries = $link->query("SELECT g.*, u.name FROM guestbook g JOIN users u ON g.user_id = u.id ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>

<h1>Гостевая книга</h1>

<?php if (isset($_SESSION['user_id'])): ?>
    <div class="guestbook-form">
        <form method="post" id="guestbookForm">
            <div class="format-buttons">
                <button type="button" class="format-button" data-format="bold">Жирный</button>
                <button type="button" class="format-button" data-format="list">Список</button>
                <button type="button" class="format-button" data-format="link">Ссылка</button>
            </div>

            <textarea name="content" id="contentArea" required placeholder="Ваше сообщение"></textarea>

            <input type="email" name="email" placeholder="Email">
            <input type="url" name="website" placeholder="Веб-сайт">

            <button type="submit">Отправить</button>
        </form>
    </div>
<?php endif; ?>

<div class="guestbook-entries">
    <?php foreach ($entries as $entry): ?>
        <div class="guestbook-entry">
            <div class="entry-content"><?= $entry['formatted_content'] ?></div>
            <div class="entry-meta">
                <span>Автор: <?= htmlspecialchars($entry['name']) ?></span>
                <?php if ($entry['email']): ?>
                    <span>Email: <?= htmlspecialchars($entry['email']) ?></span>
                <?php endif; ?>
                <?php if ($entry['website']): ?>
                    <span>Сайт: <a href="<?= htmlspecialchars($entry['website']) ?>" target="_blank"><?= htmlspecialchars($entry['website']) ?></a></span>
                <?php endif; ?>
                <span>Дата: <?= formatDate($entry['created_at']) ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const formatButtons = document.querySelectorAll('.format-button');
        const contentArea = document.getElementById('contentArea');

        formatButtons.forEach(button => {
            button.addEventListener('click', function() {
                const format = this.dataset.format;
                const start = contentArea.selectionStart;
                const end = contentArea.selectionEnd;
                const text = contentArea.value;
                const selection = text.substring(start, end);

                let replacement = '';
                switch (format) {
                    case 'bold':
                        replacement = `**${selection}**`;
                        break;
                    case 'list':
                        replacement = `\n- ${selection}`;
                        break;
                    case 'link':
                        replacement = selection ? `[${selection}](url)` : `[текст](url)`;
                        break;
                }

                contentArea.value = text.substring(0, start) + replacement + text.substring(end);
                contentArea.focus();
            });
        });
    });
</script>

<?php require "footer.php"; ?>