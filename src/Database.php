<?php
namespace App;

use PDO;

class Database
{
    public static function connect(): PDO
    {
        // База данных SQLite будет лежать в корне папки lab3 в файле data.sqlite
        $pdo = new PDO('sqlite:' . __DIR__ . '/../data.sqlite');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Создаем таблицу пользователей (если её еще нет)
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            login TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            bg_color TEXT DEFAULT '#ffffff',
            font_color TEXT DEFAULT '#000000',
            remember_token TEXT
        )");
        
        return $pdo;
    }
}