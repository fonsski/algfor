<?php
$host = "localhost";
$user = "root";
$pass = "";
$db_name = "learnproject";

// Подключение к базе данных
$link = new mysqli($host, $user, $pass, $db_name);
if ($link->connect_error) {
    die("Ошибка подключения: " . $link->connect_error);
}

// Очистка существующих данных
$link->query("SET FOREIGN_KEY_CHECKS = 0");
$tables = ['users', 'notes', 'comments', 'tests', 'questions', 'graphics'];
foreach($tables as $table) {
    $link->query("TRUNCATE TABLE $table");
}
$link->query("SET FOREIGN_KEY_CHECKS = 1");

// Функция для хеширования пароля
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Добавляем пользователей
$users_data = [
    ['Администратор', 'admin@example.com', '123456', 'admin'],
    ['Иван Иванов', 'ivan@example.com', '123456', 'user'],
    ['Петр Петров', 'petr@example.com', '123456', 'user'],
    ['Мария Сидорова', 'maria@example.com', '123456', 'user']
];

foreach ($users_data as $user) {
    $password = hashPassword($user[2]);
    $stmt = $link->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $user[0], $user[1], $password, $user[3]);
    $stmt->execute();
}

// 2. Затем добавляем тесты
$sql = "INSERT INTO tests (title, description) VALUES 
    ('Основы алгебры', 'Основные алгебраические понятия'),
    ('Функции', 'Линейные и квадратичные функции'),
    ('Уравнения', 'Методы решения уравнений')";
if(!$link->query($sql)) {
    die("Ошибка добавления тестов: " . $link->error);
}

// 3. Добавляем заметки по алгебре
$notes_data = [
    ['Линейные функции', 'Линейная функция y = kx + b, где k - угловой коэффициент, b - точка пересечения с осью Y', 1],
    ['Квадратные уравнения', 'Формула дискриминанта: D = b² - 4ac. Корни: x = (-b ± √D) / (2a)', 1],
    ['Системы уравнений', 'Методы решения: подстановка, сложение, графический метод', 1]
];

foreach ($notes_data as $note) {
    $stmt = $link->prepare("INSERT INTO notes (title, content, author_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $note[0], $note[1], $note[2]);
    $stmt->execute();
}

// Добавляем тестовые заметки
$notes_data = [
    ['Квадратные уравнения', 'Квадратное уравнение — это уравнение вида ax² + bx + c = 0, где a ≠ 0', 1],
    ['Линейная функция', 'Линейная функция имеет вид y = kx + b, где k - угловой коэффициент', 1],
    ['Системы уравнений', 'Методы решения систем линейных уравнений...', 1]
];

foreach ($notes_data as $note) {
    $stmt = $link->prepare("INSERT INTO notes (title, content, author_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $note[0], $note[1], $note[2]);
    $stmt->execute();
}

// 4. Добавляем комментарии
$sql = "INSERT INTO comments (note_id, user_id, content) VALUES
    (1, 2, 'Интересная заметка!'),
    (2, 1, 'Спасибо за информацию!')";
if(!$link->query($sql)) {
    die("Ошибка добавления комментариев: " . $link->error);
}

// Добавляем тестовые комментарии
$comments_data = [
    [1, 2, 'Отличное объяснение теоремы!'],
    [1, 3, 'Очень понятно написано'],
    [2, 4, 'Спасибо за формулу']
];

foreach ($comments_data as $comment) {
    $stmt = $link->prepare("INSERT INTO comments (note_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $comment[0], $comment[1], $comment[2]);
    $stmt->execute();
}

// 5. Добавляем вопросы к тестам
$sql = "INSERT INTO questions (test_id, question, correct_answer, options) VALUES
    (1, 'Что такое корень уравнения?', 'Значение переменной, обращающее уравнение в верное равенство', '[\"Значение переменной, обращающее уравнение в верное равенство\",\"Любое число в уравнении\",\"Коэффициент при переменной\"]'),
    (1, 'Как найти дискриминант квадратного уравнения?', 'b² - 4ac', '[\"b² - 4ac\",\"a² + b²\",\"2ab\"]'),
    (1, 'Что показывает угловой коэффициент?', 'Наклон прямой к оси X', '[\"Наклон прямой к оси X\",\"Точку пересечения с осью Y\",\"Длину отрезка\"]')";
if(!$link->query($sql)) {
    die("Ошибка добавления вопросов: " . $link->error);
}

// Добавляем тестовые опросы
$polls_data = [
    ['Какая геометрическая фигура вам нравится больше?', 
     json_encode(['Треугольник', 'Квадрат', 'Круг', 'Ромб'])],
    ['Какой раздел геометрии сложнее?', 
     json_encode(['Планиметрия', 'Стереометрия', 'Аналитическая геометрия'])]
];

foreach ($polls_data as $poll) {
    $stmt = $link->prepare("INSERT INTO polls (question, options) VALUES (?, ?)");
    $stmt->bind_param("ss", $poll[0], $poll[1]);
    $stmt->execute();
}

// Добавляем тестовые голоса
$votes_data = [
    [1, 2, 0],
    [1, 3, 1],
    [1, 4, 2],
    [2, 2, 1],
    [2, 3, 0]
];

foreach ($votes_data as $vote) {
    $stmt = $link->prepare("INSERT INTO poll_votes (poll_id, user_id, option_id) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $vote[0], $vote[1], $vote[2]);
    $stmt->execute();
}

// Добавляем записи в гостевую книгу
$guestbook_data = [
    [2, 'Отличный сайт!', '<p>Отличный сайт!</p>', 'http://ivan.ru'],
    [3, 'Спасибо за помощь', '<p>Спасибо за помощь</p>', null]
];

foreach ($guestbook_data as $entry) {
    $stmt = $link->prepare("INSERT INTO guestbook (user_id, content, formatted_content, website) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $entry[0], $entry[1], $entry[2], $entry[3]);
    $stmt->execute();
}

echo "Тестовые данные успешно добавлены!\n";
$link->close();
