<?php
require_once "header.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (login($email, $password)) {
        header("Location: /index.php");
        exit();
    } else {
        $error = "Неверный email или пароль.";
    }
}
?>

<h1>Вход</h1>

<?php if (isset($error)): ?>
    <p style="color: red;"><?= $error ?></p>
<?php endif; ?>

<form method="post">
    <label>Email:</label>
    <input type="email" name="email" required>

    <label>Пароль:</label>
    <input type="password" name="password" required>

    <button type="submit">Войти</button>
</form>

<?php require "footer.php"; ?>