<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../../../models/ProgramacaoModel.php';

$programacaoModel = new ProgramacaoModel();

$codigo = $_GET['codigo'];

if (strlen($codigo) < 9) {
    http_response_code(400);
    echo json_encode(['erro' => 'Código inválido']);
    exit;
}

$registro = $programacaoModel->buscarPorCodigo($codigo);

if (!$registro) {
    http_response_code(404);
    echo json_encode(['erro' => 'Registro não encontrado']);
    exit;
}



echo json_encode($registro);
exit;