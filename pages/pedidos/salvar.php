<?php
require_once __DIR__ . '/../../models/PedidoModel.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?page=Pedidos');
    exit;
}

$dados = [
    'pedido'    => $_POST['pedido']    ?? null,
    'tipo'       => $_POST['tipo_pedido']       ?? null,
    'data'     => $_POST['data']     ?? null,
];

echo $dados['tipo'];

$model = new PedidoModel();

try {
    $model->salvar($dados);
    header('Location: index.php?page=pedidos&status=sucesso');
    exit;
} catch (PDOException $e) {
    header('Location: index.php?page=pedidos&status=' . $e->getMessage());
    exit;
}

