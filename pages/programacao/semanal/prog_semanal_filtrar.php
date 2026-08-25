<?php

require_once __DIR__ . '/../../../models/tabelasModel.php';
require_once __DIR__ . '/../../../includes/helpers.php';

$semana = $_GET['semana'] ?? date('o-\WW');
$intervalo = calcularIntervaloSemana($semana);
$recursoId = $_GET['recurso_filtro'] ?? null;

$tabelaModel = new tabelasModel();

$tabela = [];
$tabela['tabSemanal'] = $tabelaModel->listarTabSemanal($intervalo['inicio'], $intervalo['fim'], $recursoId);

$dias = [
    1 => ['nome' => 'Segunda-feira', 'id' => 'segunda'],
    2 => ['nome' => 'Terça-feira', 'id' => 'terca'],
    3 => ['nome' => 'Quarta-feira', 'id' => 'quarta'],
    4 => ['nome' => 'Quinta-feira', 'id' => 'quinta'],
    5 => ['nome' => 'Sexta-feira', 'id' => 'sexta'],
];
$hoje = date('Y-m-d');

include __DIR__ . '/dias.php';
exit;