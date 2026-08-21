<?php

require_once __DIR__ . '/../../../models/ProgramacaoModel.php';

header('Content-Type: application/json');

$dadosRecebidos = json_decode(file_get_contents('php://input'), true);
$itens = $dadosRecebidos['itens'] ?? [];

if (empty($itens)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Nenhum item recebido']);
    return;
}

$model = new ProgramacaoModel();

try {
    $model->reordenarSemanal($itens);
    echo json_encode(['sucesso' => true]);

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
}

exit;