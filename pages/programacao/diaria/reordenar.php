<?php

require_once __DIR__ . '/../../../models/ProgramacaoModel.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$itens = $input['itens'] ?? [];

if (empty($itens)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Nenhum item recebido']);
    exit;
}

$model = new ProgramacaoModel();

try {
    $model->atualizarOrdem($itens);
    echo json_encode(['sucesso' => true]);
    
} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
}

exit;