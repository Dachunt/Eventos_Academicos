<?php

declare(strict_types=1);

namespace App\Config;

use PDO;

final class Database
{
    // [CONCEPTO] Encapsulación: los detalles de conexión están concentrados en una clase.
    public static function conectar(): PDO
    {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: '3307';
        $name = getenv('DB_NAME') ?: 'eventos_db';
        $user = getenv('DB_USER') ?: 'eventos_user';
        $password = getenv('DB_PASSWORD') ?: 'eventos_password';
        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

        // [CONCEPTO] Abstracción: el resto de la aplicación recibe una conexión lista para usar.
        return new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
}