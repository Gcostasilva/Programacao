<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../../../models/ProgramacaoModel.php';

$programacaoModel = new ProgramacaoModel();

$pedido = trim($_GET['pedido'] ?? '');

if ($pedido === '') {
    echo json_encode([]);
    exit;
}

$registros = $programacaoModel->buscarPorPedido($pedido);

echo json_encode($registros);
exit;