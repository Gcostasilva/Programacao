<?php
require_once __DIR__ . '/../../../models/ProgramacaoQuinzenalModel.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $dados = json_decode(file_get_contents('php://input'), true);
    $quinzena = trim((string)($dados['quinzena'] ?? ''));
    $itens = $dados['itens'] ?? [];

    if (!is_array($itens)) {
        throw new InvalidArgumentException('Lista de itens inválida.');
    }

    (new ProgramacaoQuinzenalModel())->reordenar($quinzena, $itens);

    echo json_encode(['sucesso' => true]);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
}
