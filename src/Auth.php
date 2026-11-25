<?php
namespace App;

class Auth
{
    // 1. Функция регистрации (сохраняет логин, пароль и цвета в БД)
    public static function register(string $login, string $password, string $bg, string $font): bool
    {
        $stmt = Database::connect()->prepare(
            "INSERT INTO users (login, password, bg_color, font_color) VALUES (?, ?, ?, ?)"
        );
        // Пароль хэшируется для безопасности
        return $stmt->execute([$login, password_hash($password, PASSWORD_DEFAULT), $bg, $font]);
    }

    // 2. Функция входа (проверяет пароль и ставит куки)
    public static function login(string $login, string $password, bool $remember): bool
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            return false; // Неверный логин или пароль
        }

        $_SESSION['user_id'] = $user['id'];

        // Ставим куки с настройками на 1 день (86400 секунд)
        setcookie('bg_color',   $user['bg_color'],   time()+86400, '/');
        setcookie('font_color', $user['font_color'], time()+86400, '/');

        // Если галочка "Запомнить" нажата
        if ($remember) {
            $token = bin2hex(random_bytes(32)); // Случайный код
            $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?")
                ->execute([$token, $user['id']]);
            // Ставим куку автологина на 30 дней (2592000 секунд)
            setcookie('remember', $token, time()+2592000, '/'); 
        }
        return true;
    }

    // 3. Функция автологина (если есть кука 'remember')
    public static function tryAutoLogin(): void
    {
        if (isset($_SESSION['user_id'])) return; // Уже вошел
        if (empty($_COOKIE['remember'])) return; // Нет куки - нечего проверять

        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE remember_token = ?");
        $stmt->execute([$_COOKIE['remember']]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($user) {
            // Восстанавливаем сессию и обновляем куки
            $_SESSION['user_id'] = $user['id'];
            setcookie('bg_color',   $user['bg_color'],   time()+86400, '/');
            setcookie('font_color', $user['font_color'], time()+86400, '/');
        }
    }

    // 4. Функция выхода (разлогин)
    public static function logout(): void
    {
        if (!empty($_SESSION['user_id'])) {
            Database::connect()
                ->prepare("UPDATE users SET remember_token = NULL WHERE id = ?")
                ->execute([$_SESSION['user_id']]);
        }
        session_destroy();
        // Удаляем куки, ставя им время в прошлом
        setcookie('remember', '', time()-3600, '/');
        setcookie('bg_color', '', time()-3600, '/');
        setcookie('font_color', '', time()-3600, '/');
    }
}