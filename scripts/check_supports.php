<?php

$pdo = new PDO('mysql:host=127.0.0.1;port=3307;dbname=crm;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = 'SELECT id, nombre_cliente, created_at, soporte_1, soporte_2, soporte_3, mesa_soporte_1, mesa_soporte_2, mesa_soporte_3
        FROM clientes
        ORDER BY id DESC
        LIMIT 10';

$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

if (! $rows) {
    echo "No hay registros en clientes." . PHP_EOL;
    exit(0);
}

foreach ($rows as $row) {
    echo str_repeat('-', 70) . PHP_EOL;
    echo 'ID: ' . $row['id'] . ' | Cliente: ' . $row['nombre_cliente'] . ' | Fecha: ' . $row['created_at'] . PHP_EOL;
    echo 'soporte_1: ' . ($row['soporte_1'] ?: '[vacio]') . PHP_EOL;
    echo 'soporte_2: ' . ($row['soporte_2'] ?: '[vacio]') . PHP_EOL;
    echo 'soporte_3: ' . ($row['soporte_3'] ?: '[vacio]') . PHP_EOL;
    echo 'mesa_soporte_1: ' . ($row['mesa_soporte_1'] ?: '[vacio]') . PHP_EOL;
    echo 'mesa_soporte_2: ' . ($row['mesa_soporte_2'] ?: '[vacio]') . PHP_EOL;
    echo 'mesa_soporte_3: ' . ($row['mesa_soporte_3'] ?: '[vacio]') . PHP_EOL;
}
