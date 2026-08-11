<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../../../models/ProgramacaoModel.php';

$programacaoModel = new ProgramacaoModel();

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID inválido']);
    exit;
}

$registro = $programacaoModel->buscarPorId($id);

if (!$registro) {
    http_response_code(404);
    echo json_encode(['erro' => 'Registro não encontrado']);
    exit;
}

echo json_encode($registro);
exit;