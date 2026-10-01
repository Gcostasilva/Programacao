<?php
require_once __DIR__ . '/../../models/EstornoModel.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['sucesso' => false, 'erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$motivoInformado = trim($_POST['motivo'] ?? '');
$dados = [
    'pedido' => trim($_POST['pedido'] ?? ''),
    'atendimento' => trim($_POST['atendimento'] ?? ''),
    'vendedor_id' => (int) ($_POST['vendedor_id'] ?? 0),
    'total_parcial' => strtolower(trim($_POST['total_parcial'] ?? '')),
    'motivo_id' => (int) ($_POST['motivo_id'] ?? 0)
];

// Compatibilidade com o formulário atual: o campo original #estornoMotivo
// continua sendo enviado como "motivo", agora contendo o ID do cadastro.
if ($dados['motivo_id'] <= 0 && ctype_digit($motivoInformado)) {
    $dados['motivo_id'] = (int) $motivoInformado;
}

if ($dados['pedido'] === '' || strlen($dados['pedido']) > 6) {
    http_response_code(422); echo json_encode(['sucesso' => false, 'erro' => 'Informe um pedido válido.'], JSON_UNESCAPED_UNICODE); exit;
}
if ($dados['atendimento'] === '' || strlen($dados['atendimento']) > 6) {
    http_response_code(422); echo json_encode(['sucesso' => false, 'erro' => 'Informe um atendimento válido.'], JSON_UNESCAPED_UNICODE); exit;
}
if ($dados['vendedor_id'] <= 0) {
    http_response_code(422); echo json_encode(['sucesso' => false, 'erro' => 'Selecione o vendedor.'], JSON_UNESCAPED_UNICODE); exit;
}
if (!in_array($dados['total_parcial'], ['total', 'parcial'], true)) {
    http_response_code(422); echo json_encode(['sucesso' => false, 'erro' => 'Selecione estorno parcial ou total.'], JSON_UNESCAPED_UNICODE); exit;
}
if ($dados['motivo_id'] <= 0) {
    http_response_code(422); echo json_encode(['sucesso' => false, 'erro' => 'Selecione o motivo do estorno.'], JSON_UNESCAPED_UNICODE); exit;
}

try {
    $model = new EstornoModel();
    $vendedores = $model->listarVendedores();
    if (!in_array($dados['vendedor_id'], array_map('intval', array_column($vendedores, 'id')), true)) {
        throw new RuntimeException('Vendedor inválido.');
    }

    $motivos = $model->listarMotivosAtivos();
    $motivo = null;
    foreach ($motivos as $m) {
        if ((int) $m['id'] === $dados['motivo_id']) { $motivo = $m; break; }
    }
    if (!$motivo) throw new RuntimeException('Motivo de estorno inválido ou inativo.');

    $dados['motivo'] = $motivo['motivo'];
    $model->registrar($dados);
    echo json_encode(['sucesso' => true], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
