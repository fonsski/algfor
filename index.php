<?php
require_once "vendor/components/header.php";

// Настройки пагинации

# Инициализация пагинации
$notes_per_page = 5;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $notes_per_page;

// Получаем общее количество заметок
$total_notes = $link->query("SELECT COUNT(*) as count FROM notes")->fetch_assoc()['count'];
$total_pages = ceil($total_notes / $notes_per_page);

// Получаем заметки для текущей страницы
$sql = "SELECT * FROM notes ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $link->prepare($sql);
$stmt->bind_param("ii", $notes_per_page, $offset);
$stmt->execute();
$notes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<h1>Заметки по алгебре</h1>

<div class="notes-grid">
    <!-- Выводим все заметки -->
    <?php foreach ($notes as $note): ?>
        <div class="note">
            <h2><?= htmlspecialchars($note['title']) ?></h2>
            <div class="note-content">
                <?php
                $short_content = mb_substr(strip_tags($note['content']), 0, 200);
                if (mb_strlen($note['content']) > 200) {
                    $short_content .= '...';
                }
                echo nl2br(htmlspecialchars($short_content));
                ?>
            </div>
            <div class="note-meta">
                <small>Автор: <?= getUserName($note['author_id']) ?> | <?= formatDate($note['created_at']) ?></small>
            </div>
            <div class="note-actions">
                <a href="/vendor/components/view_note.php?id=<?= $note['id'] ?>" class="button">Читать далее</a>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="/vendor/admin/edit_note.php?id=<?= $note['id'] ?>" class="button">Редактировать</a>
                    <a href="/vendor/admin/delete_note.php?id=<?= $note['id'] ?>" class="button"
                        onclick="return confirm('Удалить заметку?')">Удалить</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<!-- Пагинация -->
<?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?>" class="page-link <?= $i === $current_page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
<?php endif; ?>

<?php require "vendor/components/footer.php"; ?>