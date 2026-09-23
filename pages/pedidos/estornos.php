<?php
require_once __DIR__ . '/../../models/EstornoModel.php';

header('Content-Type: application/json; charset=utf-8');

$pedido = trim($_GET['pedido'] ?? '');

if ($pedido === '') {
    echo json_encode([]);
    exit;
}

try {
    $model = new EstornoModel();
    echo json_encode($model->listarPorPedido($pedido), JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Não foi possível consultar os estornos do pedido.'], JSON_UNESCAPED_UNICODE);
}
