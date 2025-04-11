<?php
require_once "header.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: /vendor/components/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $type = filter_input(INPUT_POST, 'function', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $vertices = $_POST['vertices'] ?? '[]';
    $calculations = $_POST['calculations'] ?? '{}';
    $params = $_POST['params'] ?? '{}';

    try {
        $stmt = $link->prepare("INSERT INTO graphics (title, type, vertices, calculations, params, author_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $title, $type, $vertices, $calculations, $params, $_SESSION['user_id']);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Ошибка сохранения']);
        }
        exit;
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// Получаем сохраненные работы пользователя
$userGraphics = getGraphics();
?>

<div class="graphics-wrapper">
    <div class="graphics-tools">
        <h2>Построение графиков функций</h2>
        <form id="graphicsForm" method="post">
            <input type="text" name="title" placeholder="Название графика" required class="graphics-input">

            <div class="shape-controls">
                <label>
                    Тип функции:
                    <select name="function" id="functionSelect" class="graphics-select">
                        <option value="linear">Линейная (kx + b)</option>
                        <option value="quadratic">Квадратичная (ax² + bx + c)</option>
                        <option value="cubic">Кубическая (ax³ + bx² + cx + d)</option>
                        <option value="exponential">Показательная (aˣ)</option>
                    </select>
                </label>

                <div id="functionParams" class="function-params">
                    <!-- Параметры добавляются динамически -->
                </div>

                <div class="graph-settings">
                    <label>
                        Цвет графика:
                        <input type="color" id="graphColor" name="graphColor" value="#3498db">
                    </label>
                    <div class="range-inputs">
                        <label>X от: <input type="number" name="xMin" value="-10"></label>
                        <label>X до: <input type="number" name="xMax" value="10"></label>
                    </div>
                </div>
            </div>

            <button type="submit" class="graphics-button">Сохранить</button>
        </form>
    </div>

    <div class="canvas-container">
        <canvas id="algebraCanvas" width="800" height="600"></canvas>
        <div id="calculations" class="calculations">
            <h3>Информация о функции</h3>
            <div id="functionInfo"></div>
        </div>
    </div>

    <?php if (!empty($userGraphics)): ?>
        <div class="saved-graphics">
            <h3>Сохраненные работы</h3>
            <div class="graphics-grid">
                <?php foreach ($userGraphics as $graphic): ?>
                    <div class="graphic-card">
                        <h4><?= htmlspecialchars($graphic['title']) ?></h4>
                        <p>Создано: <?= formatDate($graphic['created_at']) ?></p>
                        <button class="load-graphic" data-params='<?= htmlspecialchars($graphic['params']) ?>'>
                            Загрузить
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="/assets/js/function-graphs.js"></script>