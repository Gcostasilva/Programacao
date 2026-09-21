<?php
require_once __DIR__ . '/../../../models/ProgramacaoQuinzenalModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?page=prog_quinzenal');
    exit;
}

$dados = [
    'id' => (int) ($_POST['id'] ?? 0),
    'quinzena' => $_POST['quinzena'] ?? null,
    'produto_id' => trim($_POST['produto_id'] ?? ''),
    'quantidade' => $_POST['quantidade'] ?? null,
    'peca_realizada' => $_POST['peca_realizada'] ?? 0,
    'ordem_producao' => trim($_POST['ordem_producao'] ?? ''),
    'obs' => trim($_POST['obs'] ?? ''),
];

try {
    (new ProgramacaoQuinzenalModel())->atualizar($dados);
    header('Location: index.php?page=prog_quinzenal&quinzena=' . urlencode($dados['quinzena']) . '&status=atualizado');
} catch (Throwable $e) {
    header('Location: index.php?page=prog_quinzenal&quinzena=' . urlencode($dados['quinzena'] ?? '') . '&status=' . urlencode($e->getMessage()));
}
exit;
