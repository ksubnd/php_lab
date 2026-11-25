<?php
require 'vendor/autoload.php';
session_start();
use App\Auth;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success = Auth::register(
        $_POST['login'],
        $_POST['password'],
        $_POST['bg_color'] ?? '#ffffff',
        $_POST['font_color'] ?? '#000000'
    );
    if ($success) {
        // Если регистрация успешна, кидаем на главную
        header('Location: index.php'); 
    } else {
        echo "Ошибка регистрации. Возможно, такой логин уже существует.";
    }
}