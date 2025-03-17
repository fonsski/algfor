<?php
require_once 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (register($name, $email, $password)) {
        header("Location: login.php");
        exit();
    } else {
        $error = "Ошибка регистрации. Возможно, email уже используется.";
    }
}
?>

<h1>Регистрация</h1>

<?php if (isset($error)): ?>
    <p style="color: red;"><?= $error ?></p>
<?php endif; ?>

<form method="post">
    <label>Имя:</label>
    <input type="text" name="name" required>

    <label>Email:</label>
    <input type="email" name="email" required>

    <label>Пароль:</label>
    <input type="password" name="password" required>

    <button type="submit">Зарегистрироваться</button>
</form>

<?php require 'footer.php'; ?>