<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../../../models/ProgramacaoModel.php';

$programacaoModel = new ProgramacaoModel();

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados inválidos']);
    exit;
}

try {

    $sucesso = $programacaoModel->excluir_prog($id);
    header('Location: index.php?page=prog_diaria&status=sucesso');
    exit;
} catch (PDOException $e) {

    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao atualizar']);
    exit;
}

exit;