<?php

require_once __DIR__ . '/../models/ProgramacaoQuinzenalModel.php';
require_once __DIR__ . '/../models/tabelasModel.php';

$model = new ProgramacaoQuinzenalModel();
$model->garantirEstrutura();
$tabelas = new tabelasModel();

$quinzena = $_GET['quinzena'] ?? date('Y-m') . '-' . (date('d') <= 15 ? '1' : '2');

$dados = [
    'produtos' => $tabelas->listaCodigos(),
    'quinzena' => $quinzena,
];

$tabela = $model->listar($quinzena);

include __DIR__ . '/../pages/programacao/quinzena/index.php';
