<?php
require_once "header.php";

// Обработка голосования
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $poll_id = $_POST['poll_id'];
    $option_id = $_POST['option_id'];
    
    $stmt = $link->prepare("INSERT INTO poll_votes (poll_id, user_id, option_id) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $poll_id, $_SESSION['user_id'], $option_id);
    $stmt->execute();
}

// Получение опросов
$polls = $link->query("SELECT * FROM polls ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>

<h1>Опросы</h1>

<div class="polls-container">
    <?php foreach ($polls as $poll): ?>
        <?php
        $options = json_decode($poll['options'], true);
        $votes = [];
        $total_votes = 0;
        
        $result = $link->query("SELECT option_id, COUNT(*) as count FROM poll_votes WHERE poll_id = {$poll['id']} GROUP BY option_id");
        while ($row = $result->fetch_assoc()) {
            $votes[$row['option_id']] = $row['count'];
            $total_votes += $row['count'];
        }
        
        $user_voted = false;
        if (isset($_SESSION['user_id'])) {
            $stmt = $link->prepare("SELECT 1 FROM poll_votes WHERE poll_id = ? AND user_id = ?");
            $stmt->bind_param("ii", $poll['id'], $_SESSION['user_id']);
            $stmt->execute();
            $user_voted = $stmt->get_result()->num_rows > 0;
        }
        ?>
        
        <div class="poll-card">
            <h3><?= htmlspecialchars($poll['question']) ?></h3>
            
            <?php if (!$user_voted && isset($_SESSION['user_id'])): ?>
                <form method="post" class="poll-form">
                    <input type="hidden" name="poll_id" value="<?= $poll['id'] ?>">
                    <?php foreach ($options as $id => $option): ?>
                        <label>
                            <input type="radio" name="option_id" value="<?= $id ?>" required>
                            <?= htmlspecialchars($option) ?>
                        </label>
                    <?php endforeach; ?>
                    <button type="submit">Голосовать</button>
                </form>
            <?php else: ?>
                <div class="poll-results">
                    <?php foreach ($options as $id => $option): ?>
                        <?php 
                        $vote_count = $votes[$id] ?? 0;
                        $percentage = $total_votes > 0 ? ($vote_count / $total_votes) * 100 : 0;
                        ?>
                        <div class="poll-option">
                            <div class="option-text"><?= htmlspecialchars($option) ?></div>
                            <div class="option-bar">
                                <div class="option-fill" style="width: <?= $percentage ?>%"></div>
                            </div>
                            <div class="option-percent"><?= round($percentage) ?>% (<?= $vote_count ?>)</div>
                        </div>
                    <?php endforeach; ?>
                    <div class="total-votes">Всего голосов: <?= $total_votes ?></div>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<?php require "footer.php"; ?>
