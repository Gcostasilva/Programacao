<?php

require_once __DIR__ . '/../services/RelatorioProgramacaoService.php';

$inicio = $_GET['inicio'] ?? date('Y-m-d', strtotime('monday this week'));
$fim = $_GET['fim'] ?? date('Y-m-d', strtotime('friday this week'));

$recursoId = null;
if (isset($_GET['recurso_id']) && $_GET['recurso_id'] !== '') {
    $recursoId = filter_var($_GET['recurso_id'], FILTER_VALIDATE_INT);

    if ($recursoId === false) {
        http_response_code(400);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'erro' => 'recurso_id inválido.'
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        return;
    }
}

$inicioValido = DateTime::createFromFormat('Y-m-d', $inicio);
$fimValido = DateTime::createFromFormat('Y-m-d', $fim);

if (!$inicioValido || !$fimValido || $inicioValido->format('Y-m-d') !== $inicio || $fimValido->format('Y-m-d') !== $fim) {
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'erro' => 'As datas devem estar no formato Y-m-d.'
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    return;
}

if ($inicio > $fim) {
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'erro' => 'A data inicial não pode ser maior que a data final.'
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    return;
}

try {
    $service = new RelatorioProgramacaoService();
    $relatorio = $service->gerar($inicio, $fim, $recursoId);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(
        $relatorio,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode([
        'erro' => 'Não foi possível gerar os dados do relatório.',
        'mensagem' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
