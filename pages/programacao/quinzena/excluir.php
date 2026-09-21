<?php
require_once __DIR__ . '/../../../models/ProgramacaoQuinzenalModel.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$quinzena = $_GET['quinzena'] ?? '';

if (!$id) {
    header('Location: index.php?page=prog_quinzenal&quinzena=' . urlencode($quinzena) . '&status=ID%20inv%C3%A1lido');
    exit;
}

try {
    (new ProgramacaoQuinzenalModel())->excluir($id);
    header('Location: index.php?page=prog_quinzenal&quinzena=' . urlencode($quinzena) . '&status=excluido');
} catch (Throwable $e) {
    header('Location: index.php?page=prog_quinzenal&quinzena=' . urlencode($quinzena) . '&status=' . urlencode($e->getMessage()));
}
exit;
