<?php
require_once __DIR__ . '/../../../models/ProgramacaoModel.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?page=prog_diaria');
    exit;
}

$dados = [
    'id' => $_POST['id'],
    'recurso' => $_POST['recurso'] ?? null,
    'data' => $_POST['data'] ?? null,
    'pedido' => $_POST['pedido'] ?? null,
    'espessura' => $_POST['espessura'] ?? null,
    'aco' => $_POST['aco'] ?? null,
    'vendedor' => $_POST['vendedor'] ?? null,
    'peso' => $_POST['peso'] ?? null,
    'observacao' => $_POST['observacao'] ?? null,
    'falta_mp' => isset($_POST['falta_mp_at']) ? 1 : 0,
];

$model = new ProgramacaoModel();

try {
    $model->atualizar($dados);
    header('Location: index.php?page=prog_diaria&status=sucesso');
    exit;
} catch (PDOException $e) {
    header('Location: index.php?page=prog_diaria&status=' . $e->getMessage());
    exit;
}