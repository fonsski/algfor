<?php
require "header.php";

$sql = "SELECT * FROM tests ORDER BY created_at DESC";
$result = $link->query($sql);
$tests = $result->fetch_all(MYSQLI_ASSOC);
?>

<h1>Доступные тесты</h1>

<div class="tests-list">
    <?php foreach ($tests as $test): ?>
        <div class="test-card">
            <h2><?= htmlspecialchars($test['title']) ?></h2>
            <p><?= htmlspecialchars($test['description']) ?></p>
            <a href="/vendor/components/test.php?test_id=<?= $test['id'] ?>" class="button">Начать тест</a>
        </div>
    <?php endforeach; ?>
</div>

<?php require "footer.php"; ?>