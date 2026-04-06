<?php

$pdo = new PDO('mysql:host=127.0.0.1;port=3307;dbname=crm;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$count = $pdo->query('SELECT COUNT(*) FROM clientes')->fetchColumn();
echo 'TOTAL=' . $count . PHP_EOL;

$row = $pdo->query('SELECT id, created_at, user_id, status, nombre_cliente FROM clientes ORDER BY id DESC LIMIT 1')
    ->fetch(PDO::FETCH_ASSOC);

if ($row) {
    echo 'ULTIMO_ID=' . $row['id'] . PHP_EOL;
    echo 'ULTIMO_NOMBRE=' . $row['nombre_cliente'] . PHP_EOL;
    echo 'ULTIMO_CREADO=' . $row['created_at'] . PHP_EOL;
    echo 'ULTIMO_ASESOR=' . $row['user_id'] . PHP_EOL;
    echo 'ULTIMO_STATUS=' . $row['status'] . PHP_EOL;
}
