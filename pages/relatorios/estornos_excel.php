<?php
require_once __DIR__ . '/../../models/EstornoModel.php';

$model = new EstornoModel();

$filtros = [
    'pedido' => trim($_GET['pedido'] ?? ''),
    'vendedor_id' => (int) ($_GET['vendedor_id'] ?? 0),
    'tipo' => trim($_GET['tipo'] ?? ''),
    'data_inicio' => trim($_GET['data_inicio'] ?? ''),
    'data_fim' => trim($_GET['data_fim'] ?? '')
];

$registros = $model->listarRelatorio($filtros);

$nomeArquivo = 'relatorio_estornos_' . date('Y-m-d_H-i-s') . '.csv';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $nomeArquivo . '"');
header('Cache-Control: no-store, no-cache, must-revalidate');

echo "\xEF\xBB\xBF";
$out = fopen('php://output', 'w');

fputcsv($out, ['RELATÓRIO DE ESTORNOS'], ';');
fputcsv($out, ['Gerado em', date('d/m/Y H:i:s')], ';');
fputcsv($out, [], ';');
fputcsv($out, ['Data/Hora', 'Pedido', 'Atendimento', 'Vendedor', 'Tipo', 'Motivo'], ';');

foreach ($registros as $r) {
    fputcsv($out, [
        $r['data_estorno'] ? date('d/m/Y H:i', strtotime($r['data_estorno'])) : '',
        $r['pedido'],
        $r['atendimento'],
        $r['vendedor'],
        $r['total_parcial'] === 'total' ? 'Total' : 'Parcial',
        $r['motivo']
    ], ';');
}

fclose($out);
exit;
