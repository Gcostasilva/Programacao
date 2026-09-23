<?php
require_once __DIR__ . '/../../models/EstornoModel.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['sucesso' => false, 'erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$dados = [
    'pedido' => trim($_POST['pedido'] ?? ''),
    'atendimento' => trim($_POST['atendimento'] ?? ''),
    'vendedor_id' => (int) ($_POST['vendedor_id'] ?? 0),
    'total_parcial' => strtolower(trim($_POST['total_parcial'] ?? '')),
    'motivo' => trim($_POST['motivo'] ?? '')
];

if ($dados['pedido'] === '' || strlen($dados['pedido']) > 6) {
    http_response_code(422);
    echo json_encode(['sucesso' => false, 'erro' => 'Informe um pedido válido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($dados['atendimento'] === '' || strlen($dados['atendimento']) > 6) {
    http_response_code(422);
    echo json_encode(['sucesso' => false, 'erro' => 'Informe um atendimento válido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($dados['vendedor_id'] <= 0) {
    http_response_code(422);
    echo json_encode(['sucesso' => false, 'erro' => 'Selecione o vendedor.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!in_array($dados['total_parcial'], ['total', 'parcial'], true)) {
    http_response_code(422);
    echo json_encode(['sucesso' => false, 'erro' => 'Selecione estorno parcial ou total.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($dados['motivo'] === '' || strlen($dados['motivo']) > 100) {
    http_response_code(422);
    echo json_encode(['sucesso' => false, 'erro' => 'Informe o motivo do estorno (até 100 caracteres).'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $model = new EstornoModel();
    $vendedor = $model->listarVendedores();
    $ids = array_map('intval', array_column($vendedor, 'id'));
    if (!in_array($dados['vendedor_id'], $ids, true)) {
        throw new RuntimeException('Vendedor inválido.');
    }

    $model->registrar($dados);
    echo json_encode(['sucesso' => true], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => 'Não foi possível registrar o estorno.'], JSON_UNESCAPED_UNICODE);
}
