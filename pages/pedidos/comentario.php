<?php
require_once __DIR__ . '/../../models/PedidosInteracaoModel.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['sucesso' => false, 'erro' => 'Método não permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$acao = $_POST['acao'] ?? 'salvar';
$pedidoId = (int) ($_POST['pedido_id'] ?? 0);

try {
    $model = new PedidosInteracaoModel();

    if ($acao === 'listar') {
        echo json_encode($model->listarComentarios($pedidoId), JSON_UNESCAPED_UNICODE);
        exit;
    }

    $comentario = trim($_POST['comentario'] ?? '');
    if ($pedidoId <= 0 || $comentario === '') {
        http_response_code(422);
        echo json_encode(['sucesso' => false, 'erro' => 'Informe um comentário.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $sucesso = $model->adicionarComentario($pedidoId, $comentario);
    echo json_encode(['sucesso' => $sucesso], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => 'Não foi possível salvar o comentário.'], JSON_UNESCAPED_UNICODE);
}
