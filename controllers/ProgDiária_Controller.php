<?php


require_once __DIR__ . '/../models/RecursoModel.php';
require_once __DIR__ . '/../models/VendedorModel.php';
require_once __DIR__ .'/../models/tabelasModel.php';
require_once __DIR__ .'/../models/ProgramacaoModel.php';
require_once __DIR__ .'/../models/CadastroAcoModel.php';

$recursoModel = new RecursoModel();
$vendedorModel = new VendedorModel();
$tabelaDiaria  = new tabelasModel();
$acoModel = new CadastroAcoModel();

$dados = [];
$tabela = [];
$idDiaria = [];


$dados['vendedores'] = $vendedorModel->listar();
$dados['recursos_diario'] = $recursoModel->listarDiario();
$dados['recursos_semanal'] = $recursoModel->listarSemanal();
$dados['tipos_aco'] = $acoModel->listarAtivos();
$tabela['tabDiaria'] = $tabelaDiaria->listarTabDiario();

include __DIR__ . '/../pages/programacao/diaria/index.php';