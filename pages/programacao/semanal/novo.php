<?php
require_once __DIR__ . '/../../../models/ProgramacaoModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?page=prog_semanal');
    exit;
}

$dados = [
    'semana' => trim($_POST['semana'] ?? ''),
    'recurso' => $_POST['recurso'] ?? null,
    'data' => $_POST['data'] ?? null,
    'demanda' => trim($_POST['demanda'] ?? ''),
    'codigo' => trim($_POST['codigo_s'] ?? ''),
    'complemento_descricao' => trim($_POST['complemento_descricao'] ?? ''),
    'quantidade' => $_POST['quantidade'] ?? null,
    'peso' => $_POST['peso'] ?? null,
    'observacao' => trim($_POST['observacao'] ?? ''),
];

try {
    if ($dados['semana'] === '' || !$dados['recurso'] || !$dados['data'] || $dados['codigo'] === '' || $dados['quantidade'] === null) {
        throw new InvalidArgumentException('Preencha os campos obrigatórios da programação semanal.');
    }

    (new ProgramacaoModel())->salvar_semanal($dados);

    header('Location: index.php?page=prog_semanal&semana=' . urlencode($dados['semana']) . '&status=sucesso');
    exit;
} catch (Throwable $e) {
    header('Location: index.php?page=prog_semanal&semana=' . urlencode($dados['semana']) . '&status=' . urlencode($e->getMessage()));
    exit;
}
