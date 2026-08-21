<?php


require_once __DIR__ . '/../models/RecursoModel.php';
require_once __DIR__ . '/../models/VendedorModel.php';
require_once __DIR__ .'/../models/tabelasModel.php';
require_once __DIR__ .'/../models/ProgramacaoModel.php';

$recursoModel = new RecursoModel();
$vendedorModel = new VendedorModel();
$tabelaDiaria  = new tabelasModel();


$dados = [];
$tabela = [];
$idDiaria = [];

$dados['vendedores'] = $vendedorModel->listar();
$dados['recursos_diario'] = $recursoModel->listarDiario();
$dados['recursos_semanal'] = $recursoModel->listarSemanal();

$tabela['tabDiaria'] = $tabelaDiaria ->listarTabDiario();
$tabela['tabSemanal'] = $tabelaDiaria ->listarTabSemanal();



include __DIR__ . '/../pages/programacao/semanal/index.php';