<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../../../models/tabelasModel.php';

$equipamento = $_GET['equipamento'] ?? null;
$data = $_GET['data'] ?? null;

$registro = [
    'dados' => []
];

$model_filtro = new tabelasModel();

$registro['dados'] = $model_filtro->listarProdDiaria(
    $equipamento,
    $data
);

echo json_encode($registro);

exit;