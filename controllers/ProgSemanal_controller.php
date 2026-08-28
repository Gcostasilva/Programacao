<?php

require_once __DIR__ . '/../models/RecursoModel.php';
require_once __DIR__ . '/../models/VendedorModel.php';
require_once __DIR__ . '/../models/tabelasModel.php';
require_once __DIR__ . '/../models/ProgramacaoModel.php';
require_once __DIR__ . '/../includes/helpers.php'; // ajuste o caminho conforme seu projeto

$recursoModel = new RecursoModel();
$vendedorModel = new VendedorModel();
$tabelaDiaria  = new tabelasModel();

$dados = [];
$tabela = [];
$idDiaria = [];

// Semana atual como padrão, igual já era feito na view
$semana = $_GET['semana'] ?? date('o-\WW');
$intervalo = calcularIntervaloSemana($semana);

$dados['vendedores'] = $vendedorModel->listar();
$dados['recursos_diario'] = $recursoModel->listarDiario();
$dados['recursos_semanal'] = $recursoModel->listarSemanal();

$tabela['tabDiaria'] = $tabelaDiaria->listarTabDiario();
$tabela['tabSemanal'] = $tabelaDiaria->listarTabSemanal($intervalo['inicio'], $intervalo['fim']);
$dados['listaCodigos'] = $tabelaDiaria->listaCodigos();

include __DIR__ . '/../pages/programacao/semanal/index.php';