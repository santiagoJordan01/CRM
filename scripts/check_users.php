<?php

$pdo = new PDO('mysql:host=127.0.0.1;port=3307;dbname=crm;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

foreach ($pdo->query('SELECT id, name, email, role FROM users ORDER BY id') as $row) {
    echo $row['id'] . ' | ' . $row['email'] . ' | ' . ($row['role'] ?? 'null') . PHP_EOL;
}
