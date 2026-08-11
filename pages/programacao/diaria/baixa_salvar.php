<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../../../models/ProgramacaoModel.php';

$programacaoModel = new ProgramacaoModel();

$id = (int) ($_POST['id'] ?? 0);
$peso_real = $_POST['peso_real'] ?? null;

if ($id <= 0 || $peso_real === null || $peso_real === '') {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados inválidos']);
    exit;
}

try {

    $sucesso = $programacaoModel->atualizarPesoReal([
        'id' => $id,
        'peso_real' => $peso_real,
    ]);

    echo json_encode(['sucesso' => $sucesso]);

} catch (PDOException $e) {

    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao atualizar']);
}

exit;