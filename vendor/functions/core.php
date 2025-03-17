<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Подключение к базе данных
$link = new mysqli("localhost", "root", "", "learnproject");
if ($link->connect_error) {
    die("Ошибка подключения: " . $link->connect_error);
}

// Функция для выхода
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

// Функции для работы с заметками 

# Функция для получения всех заметок
function getNotes()
{
    global $link;
    $result = $link->query("SELECT * FROM notes ORDER BY created_at DESC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

# Функция добавления заметки
function addNote($title, $content, $author_id)
{
    global $link;
    $stmt = $link->prepare("INSERT INTO notes (title, content, author_id, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("ssi", $title, $content, $author_id);
    return $stmt->execute();
}

# Функция удаления заметки
function deleteNote($note_id)
{
    global $link;
    if ($_SESSION['role'] !== 'admin') {
        return false; // Только админ может удалять
    }
    $stmt = $link->prepare("DELETE FROM notes WHERE id = ?");
    $stmt->bind_param("i", $note_id);
    return $stmt->execute();
}

# Функция обновления заметки
function updateNote($id, $title, $content)
{
    global $link;
    if ($_SESSION['role'] !== 'admin') {
        return false;
    }
    $stmt = $link->prepare("UPDATE notes SET title = ?, content = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $content, $id);
    return $stmt->execute();
}

// Функции для работы с комментариями

# Функция для получения всех комментариев
function getComments($note_id)
{
    global $link;
    $stmt = $link->prepare("SELECT * FROM comments WHERE note_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $note_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

# Функция добавления комментария
function addComment($note_id, $user_id, $content)
{
    global $link;
    $stmt = $link->prepare("INSERT INTO comments (note_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $note_id, $user_id, $content);
    return $stmt->execute();
}

# Функция удаления комментария
function deleteComment($comment_id)
{
    global $link;
    if ($_SESSION['role'] !== 'admin') {
        return false; // Только админ может удалять
    }
    $stmt = $link->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->bind_param("i", $comment_id);
    return $stmt->execute();
}

// Функции авторизации и регистрации в системе

# Функция регистрации
function register($name, $email, $password)
{
    global $link;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $link->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
    $stmt->bind_param("sss", $name, $email, $hashed_password);

    return $stmt->execute();
}

# Функция авторизации
function login($email, $password)
{
    global $link;
    if (empty($email) || empty($password)) {
        return false;
    }
    
    $stmt = $link->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            return true;
        }
    }
    return false;
}

// Выход из системы
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Добавляем функции для работы с тестами
function getQuestion($id) {
    global $link;
    $stmt = $link->prepare("SELECT * FROM questions WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getTotalQuestions() {
    global $link;
    $result = $link->query("SELECT COUNT(*) as total FROM questions");
    return $result->fetch_assoc()['total'];
}

// Добавляем функции для работы с графикой
function saveGraphics($title, $params, $author_id) {
    global $link;
    $stmt = $link->prepare("INSERT INTO graphics (title, params, author_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $title, $params, $author_id);
    return $stmt->execute();
}

function getGraphics($id = null) {
    global $link;
    if ($id) {
        $stmt = $link->prepare("SELECT * FROM graphics WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    return $link->query("SELECT * FROM graphics ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
}

function getUserName($user_id) {
    global $link;
    $stmt = $link->prepare("SELECT name FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    return $user ? htmlspecialchars($user['name']) : 'Неизвестный пользователь';
}

function formatDate($date) {
    return date('d.m.Y H:i', strtotime($date));
}

// Функции для работы с гостевой книгой
function addGuestbookEntry($user_id, $content, $formatted_content, $email = null, $website = null, $phone = null) {
    global $link;
    $stmt = $link->prepare("INSERT INTO guestbook (user_id, content, formatted_content, email, website, phone) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $user_id, $content, $formatted_content, $email, $website, $phone);
    return $stmt->execute();
}

function getGuestbookEntries() {
    global $link;
    return $link->query("SELECT g.*, u.name FROM guestbook g JOIN users u ON g.user_id = u.id ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
}

// Функции для работы с опросами
function createPoll($question, $options) {
    global $link;
    $options_json = json_encode($options);
    $stmt = $link->prepare("INSERT INTO polls (question, options) VALUES (?, ?)");
    $stmt->bind_param("ss", $question, $options_json);
    return $stmt->execute();
}

function vote($poll_id, $user_id, $option_id) {
    global $link;
    $stmt = $link->prepare("INSERT INTO poll_votes (poll_id, user_id, option_id) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $poll_id, $user_id, $option_id);
    return $stmt->execute();
}
