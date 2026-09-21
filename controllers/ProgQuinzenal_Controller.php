<?php

require_once __DIR__ . '/../models/ProgramacaoQuinzenalModel.php';
require_once __DIR__ . '/../models/ProdutoModel.php';

$model = new ProgramacaoQuinzenalModel();
$model->garantirEstrutura();
$produtosModel = new ProdutoModel();

$quinzena = $_GET['quinzena'] ?? date('Y-m') . '-' . (date('d') <= 15 ? '1' : '2');

$dados = [
    'produtos' => $produtosModel->listarCodigosQuinzenal(),
    'quinzena' => $quinzena,
];

$tabela = $model->listar($quinzena);

include __DIR__ . '/../pages/programacao/quinzena/index.php';
