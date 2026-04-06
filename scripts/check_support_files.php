<?php

$pdo = new PDO('mysql:host=127.0.0.1;port=3307;dbname=crm;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$row = $pdo->query('SELECT id, soporte_1, soporte_2, soporte_3 FROM clientes ORDER BY id DESC LIMIT 1')
    ->fetch(PDO::FETCH_ASSOC);

if (! $row) {
    echo "No hay clientes." . PHP_EOL;
    exit(0);
}

$base = __DIR__ . '/../storage/app/public/';

echo 'Cliente ID: ' . $row['id'] . PHP_EOL;
for ($i = 1; $i <= 3; $i++) {
    $key = 'soporte_' . $i;
    $path = $row[$key];
    if (! $path) {
        echo $key . ': [vacio]' . PHP_EOL;
        continue;
    }
    $full = $base . $path;
    echo $key . ': ' . $path . ' | existe=' . (file_exists($full) ? 'SI' : 'NO') . PHP_EOL;
}
