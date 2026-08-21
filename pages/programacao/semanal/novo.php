<?php
require_once __DIR__ . '/../../../models/ProgramacaoModel.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?page=prog_semanal');
    exit;
}

$dados = [
    'semana'        => $_POST['semana']        ?? null,
    'recurso'       => $_POST['recurso']       ?? null,
    'data'          => $_POST['data']     ?? null,
    'demanda'      => $_POST['demanda']       ?? null,
    'codigo'        => $_POST['codigo_s']  ?? null,
    'quantidade'    => $_POST['quantidade']        ?? null,
    'peso'          => $_POST['peso']   ?? null,
    'observacao'    => $_POST['observacao'] ?? null,
];

$model = new ProgramacaoModel();

try {
    $model->salvar_semanal($dados);
    header('Location: index.php?page=prog_semanal&status=sucesso');
    exit;
} catch (PDOException $e) {
    header('Location: index.php?page=prog_semanal&status=' . $e->getMessage());
    exit;
}

