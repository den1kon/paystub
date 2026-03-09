<?php

declare(strict_types=1);

try {
    // MariaDB PDO Connection
    $host = 'localhost';
    $dbname = 'paystub';
    $user = 'root';
    $password = 'password';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "Connected successfully\n";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

try {
    $sql = "
    CREATE TABLE IF NOT EXISTS companies (
        id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
        name VARCHAR(40) UNIQUE NOT NULL,
        alias VARCHAR(40),
        createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        isDeleted TINYINT(1) DEFAULT 0
    ) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $pdo->exec($sql);
    echo "Table created successfully\n";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
