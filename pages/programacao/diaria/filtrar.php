<?php
require_once __DIR__ . '/../../../models/tabelasModel.php';


$exibirBaixados = isset($_GET['exibir_baixados']) && $_GET['exibir_baixados'] === '1';
$equipamento = $_GET['equipamento'] ?? null;
$data = $_GET['data'] ?? null;


$registro['dados'] = [];

if ($data !== null && $equipamento !== null) {
    $model_filtro = new tabelasModel();
    $registro['dados'] = $model_filtro->listarProdDiaria($equipamento, $data);
}

if ($data === null || $equipamento === null) {
$model = new tabelasModel();
$tabela['tabDiaria'] = $model->listarTabDiario($exibirBaixados);

// Renderiza o tabela.php inteiro, mas capturando a saída em vez de imprimir direto na tela
ob_start();
include __DIR__ . '/componentes/tabela.php';
$htmlCompleto = ob_get_clean();

// Recorta só o pedaço entre os marcadores
$inicio = strpos($htmlCompleto, '<!-- INICIO_LINHAS -->') + strlen('<!-- INICIO_LINHAS -->');
$fim = strpos($htmlCompleto, '<!-- FIM_LINHAS -->');

echo substr($htmlCompleto, $inicio, $fim - $inicio);
}