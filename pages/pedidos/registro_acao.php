<?php
require_once __DIR__ . '/../../models/PedidosInteracaoModel.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['sucesso' => false, 'erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$acao = trim($_POST['acao'] ?? '');

if (!$id || $id <= 0 || !in_array($acao, ['cancelar_saida', 'excluir_entrada'], true)) {
    http_response_code(422);
    echo json_encode(['sucesso' => false, 'erro' => 'Ação ou entrada inválida.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $model = new PedidosInteracaoModel();
    $resultado = $acao === 'cancelar_saida'
        ? $model->cancelarSaida($id)
        : $model->excluirEntrada($id);

    echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
} catch (DomainException $e) {
    http_response_code(409);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => 'Não foi possível processar a alteração.'], JSON_UNESCAPED_UNICODE);
}
