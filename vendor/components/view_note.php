<?php
require_once "header.php";

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
?>

<div class="note-full">
    <h1><?= htmlspecialchars($note['title']) ?></h1>
    
    <div class="note-meta">
        <small>
            Автор: <?= getUserName($note['author_id']) ?> | 
            Опубликовано: <?= formatDate($note['created_at']) ?>
        </small>
    </div>

    <div class="note-content">
        <?= nl2br(htmlspecialchars($note['content'])) ?>
    </div>

    <div class="note-actions">
        <a href="/index.php" class="button">← Назад к списку</a>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="/vendor/components/edit_note.php?id=<?= $note['id'] ?>" class="button">Редактировать</a>
            <a href="/vendor/functions/delete_note.php?id=<?= $note['id'] ?>" class="button"
                onclick="return confirm('Удалить заметку?')">Удалить</a>
        <?php endif; ?>
    </div>
</div>

<?php require "footer.php"; ?>
