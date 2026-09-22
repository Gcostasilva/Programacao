<?php
require_once __DIR__ . '/../../models/tabelasModel.php';

header('Content-Type: application/json; charset=utf-8');

$pedido = trim($_GET['pedido'] ?? '');

if ($pedido === '') {
    echo json_encode([]);
    exit;
}

try {
    $model = new tabelasModel();
    echo json_encode($model->tabelaPedidos_filtrado($pedido), JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'erro' => 'Não foi possível consultar as entradas do pedido.'
    ], JSON_UNESCAPED_UNICODE);
}
