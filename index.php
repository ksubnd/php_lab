<?php
require 'vendor/autoload.php';
session_start();

use App\Auth;

// Проверяем, есть ли кука для автовхода
Auth::tryAutoLogin();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Если нажали кнопку "Выйти"
if ($action === 'logout') {
    Auth::logout();
    header('Location: index.php');
    exit;
}

// Если отправили форму входа
if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (Auth::login($_POST['login'], $_POST['password'], !empty($_POST['remember']))) {
        header('Location: index.php'); // Успешный вход
        exit;
    }
    $error = 'Неверный логин или пароль';
}

// Читаем цвета из кук (или ставим дефолтные)
$bg   = $_COOKIE['bg_color']   ?? '#ffffff';
$font = $_COOKIE['font_color'] ?? '#000000';
// Проверяем, авторизован ли пользователь
$loggedIn = isset($_SESSION['user_id']);
?>
<!doctype html>
<html>
<head><title>Lab 3 - Cookies</title></head>
<body style="background:<?= htmlspecialchars($bg) ?>; color:<?= htmlspecialchars($font) ?>; font-family: sans-serif; padding: 20px;">

<?php if ($loggedIn): ?>
    <h1>Вы авторизованы!</h1>
    <p>Ваши настройки применились из Cookie.</p>
    <a href="?action=logout" style="color:<?= htmlspecialchars($font) ?>;">Выйти</a>
<?php else: ?>
    <h1>Авторизация</h1>
    <?php if (!empty($error)) echo "<p style='color:red'>$error</p>"; ?>
    
    <form method="post">
        <input type="hidden" name="action" value="login">
        Логин: <input name="login" required><br><br>
        Пароль: <input type="password" name="password" required><br><br>
        <label><input type="checkbox" name="remember"> Запомнить меня</label><br><br>
        <button type="submit">Войти</button>
    </form>

    <hr style="margin: 30px 0;">
    
    <h2>Регистрация</h2>
    <form method="post" action="register.php">
        Логин: <input name="login" required><br><br>
        Пароль: <input type="password" name="password" required><br><br>
        Цвет фона: <input type="color" name="bg_color" value="#ffffff"><br><br>
        Цвет шрифта: <input type="color" name="font_color" value="#000000"><br><br>
        <button type="submit">Зарегистрироваться</button>
    </form>
<?php endif; ?>

</body>
</html>