<?php

require_once __DIR__ . '/../models/RecursoModel.php';
require_once __DIR__ . '/../models/VendedorModel.php';
require_once __DIR__ . '/../models/tabelasModel.php';
require_once __DIR__ . '/../models/ProdutoModel.php';
require_once __DIR__ . '/../includes/helpers.php';

$recursoModel = new RecursoModel();
$vendedorModel = new VendedorModel();
$tabelaDiaria = new tabelasModel();
$produtoModel = new ProdutoModel();

$dados = [];
$tabela = [];
$semana = $_GET['semana'] ?? date('o-\WW');
$intervalo = calcularIntervaloSemana($semana);

$dados['vendedores'] = $vendedorModel->listar();
$dados['recursos_diario'] = $recursoModel->listarDiario();
$dados['recursos_semanal'] = $recursoModel->listarSemanal();
$dados['listaCodigos'] = $produtoModel->listarCodigosProgramacao();

$tabela['tabDiaria'] = $tabelaDiaria->listarTabDiario();
$tabela['tabSemanal'] = $tabelaDiaria->listarTabSemanal($intervalo['inicio'], $intervalo['fim']);
$dados['prodSemanal'] = [];

include __DIR__ . '/../pages/programacao/semanal/index.php';
