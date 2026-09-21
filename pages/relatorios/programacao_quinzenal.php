<?php

require_once __DIR__ . '/../../services/RelatorioProgramacaoService.php';
require_once __DIR__ . '/../../models/RecursoModel.php';

$inicioPadrao = date('Y-m-d', date('j') <= 15 ? strtotime('first day of this month') : strtotime('16th day of this month'));
$fimPadrao = date('Y-m-d', date('j') <= 15 ? strtotime('15th day of this month') : strtotime('last day of this month'));

$inicio = $_GET['inicio'] ?? $inicioPadrao;
$fim = $_GET['fim'] ?? $fimPadrao;
$recursoId = isset($_GET['recurso_id']) && $_GET['recurso_id'] !== ''
    ? filter_var($_GET['recurso_id'], FILTER_VALIDATE_INT)
    : null;

$recursoModel = new RecursoModel();
$recursos = $recursoModel->listarSemanal();
$relatorio = null;
$erro = null;

function rqData(string $data): string
{
    return (new DateTime($data))->format('d/m/Y');
}

function rqNumero(float $valor): string
{
    return number_format($valor, 0, ',', '.');
}

try {
    $inicioValido = DateTime::createFromFormat('Y-m-d', $inicio);
    $fimValido = DateTime::createFromFormat('Y-m-d', $fim);

    if (!$inicioValido || !$fimValido || $inicioValido->format('Y-m-d') !== $inicio || $fimValido->format('Y-m-d') !== $fim) {
        throw new InvalidArgumentException('Informe datas válidas.');
    }

    if ($inicio > $fim) {
        throw new InvalidArgumentException('A data inicial não pode ser maior que a data final.');
    }

    $relatorio = (new RelatorioProgramacaoService())->gerar($inicio, $fim, $recursoId);
} catch (Throwable $e) {
    $erro = $e->getMessage();
}
?>

<div class="container-fluid py-3 relatorio-modelo">
    <div class="d-flex justify-content-between align-items-center mb-3 relatorio-toolbar">
        <div>
            <h2 class="mb-1">Relatório de Programação — Quinzenal</h2>
            <div class="text-muted small">Modelo RP 05 — programação de produto padrão de Corte e Dobra</div>
        </div>
        <div class="d-flex gap-2">
            <a href="?page=relatorios" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Relatórios</a>
            <?php if ($relatorio && !empty($relatorio['equipamentos'])): ?>
                <button type="button" class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir</button>
            <?php endif; ?>
        </div>
    </div>

    <form method="get" class="card shadow-sm mb-3 relatorio-toolbar">
        <input type="hidden" name="page" value="relatorio_programacao_quinzenal">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Data inicial</label>
                    <input type="date" name="inicio" class="form-control" value="<?= htmlspecialchars($inicio) ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Data final</label>
                    <input type="date" name="fim" class="form-control" value="<?= htmlspecialchars($fim) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Equipamento</label>
                    <select name="recurso_id" class="form-select">
                        <option value="">Todos os equipamentos</option>
                        <?php foreach ($recursos as $recurso): ?>
                            <option value="<?= (int) $recurso['id'] ?>" <?= $recursoId === (int) $recurso['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($recurso['descricao']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100"><i class="bi bi-search"></i> Gerar</button>
                </div>
            </div>
        </div>
    </form>

    <?php if ($erro): ?>
        <div class="alert alert-danger relatorio-toolbar"><?= htmlspecialchars($erro) ?></div>
    <?php elseif ($relatorio && empty($relatorio['equipamentos'])): ?>
        <div class="alert alert-info relatorio-toolbar">Não há programação para o período e equipamento selecionados.</div>
    <?php elseif ($relatorio): ?>
        <?php foreach ($relatorio['equipamentos'] as $equipamento): ?>
            <article class="modelo-documento">
                <header class="doc-cabecalho">
                    <div class="doc-cabecalho-top">
                        <div class="doc-marca"><strong>PERFINASA</strong><small>PERFILADOS DE AÇO</small></div>
                        <div class="doc-titulo"><strong>SISTEMA DE GESTÃO DA QUALIDADE</strong><span>REGISTRO DE PROCESSO</span></div>
                        <div class="doc-codigo"><strong>Identificação: RP 05</strong><span>Revisão: 04</span></div>
                    </div>
                    <div class="doc-titulo-principal">PROGRAMAÇÃO DE PRODUTO PADRÃO DE CORTE E DOBRA</div>
                    <div class="doc-meta">
                        <span><strong>Data Prevista Inicial:</strong> <?= rqData($inicio) ?></span>
                        <span><strong>Data Prevista Final:</strong> <?= rqData($fim) ?></span>
                        <span><strong>Equipamento:</strong> <?= htmlspecialchars($equipamento['nome']) ?></span>
                    </div>
                </header>

                <div class="doc-corpo">
                    <div class="doc-secao-titulo">CÓD — DESCRIÇÃO — QUANTIDADE DE PEÇAS</div>
                    <table class="doc-tabela">
                        <thead><tr><th>CÓD</th><th>DESCRIÇÃO</th><th>QUANTIDADE DE PEÇAS</th><th>PESO ESTIMADO (KG)</th></tr></thead>
                        <tbody>
                            <?php foreach ($equipamento['dias'] as $dia): ?>
                                <tr class="linha-data"><td colspan="4"><?= rqData($dia['data']) ?></td></tr>
                                <?php foreach ($dia['itens'] as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars((string) $item['produto_id']) ?></td>
                                        <td><?= htmlspecialchars($item['descricao']) ?></td>
                                        <td class="num"><?= rqNumero((float) $item['quantidade']) ?></td>
                                        <td class="num"><?= rqNumero((float) $item['peso_estimado']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot><tr><th colspan="2">TOTAL</th><th class="num"><?= rqNumero((float) $equipamento['totais']['quantidade']) ?></th><th class="num"><?= rqNumero((float) $equipamento['totais']['peso_estimado']) ?></th></tr></tfoot>
                    </table>

                    <div class="doc-observacoes"><strong>Observações:</strong></div>
                    <div class="doc-objetivo"><strong>Objetivo:</strong> Registrar a programação de produção do produto padrão para um determinado período.</div>
                </div>

                <footer class="doc-rodape">
                    <div><strong>Programação:</strong> Analista de PCP</div>
                    <div><strong>Análise:</strong> Supervisor de Produção</div>
                    <div><strong>Aprovação:</strong> Gerente Industrial</div>
                    <div><strong>Responsável:</strong> ENCARREGADO DE PRODUÇÃO</div>
                    <div class="doc-rodape-sistema">Sistema de Gestão da Qualidade — Identificação: RP 05 — Liberado em 04/11/2020</div>
                </footer>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
.relatorio-modelo{font-family:Arial,Helvetica,sans-serif;color:#212529}.modelo-documento{max-width:1120px;margin:0 auto 20px;background:#fff;border:1px solid #343a40}.doc-cabecalho{border-bottom:1px solid #343a40}.doc-cabecalho-top{display:grid;grid-template-columns:1.15fr 2fr 1fr;min-height:62px;border-bottom:1px solid #343a40}.doc-marca,.doc-titulo,.doc-codigo{padding:7px 10px;display:flex;flex-direction:column;justify-content:center}.doc-marca{border-right:1px solid #343a40}.doc-marca strong{font-size:1.3rem}.doc-marca small{font-size:.55rem;letter-spacing:.1em}.doc-titulo{text-align:center;font-size:.72rem;line-height:1.4}.doc-codigo{font-size:.68rem;line-height:1.5}.doc-titulo-principal{text-align:center;font-weight:800;font-size:.9rem;padding:7px;border-bottom:1px solid #343a40}.doc-meta{display:flex;justify-content:space-between;gap:12px;padding:6px 9px;font-size:.67rem}.doc-corpo{padding:10px}.doc-secao-titulo{font-weight:800;font-size:.74rem;margin-bottom:5px}.doc-tabela{width:100%;border-collapse:collapse;table-layout:fixed}.doc-tabela th,.doc-tabela td{border:1px solid #adb5bd;padding:4px 5px;font-size:.67rem;line-height:1.2}.doc-tabela th{background:#f1f3f5}.doc-tabela th:first-child{width:14%}.doc-tabela th:nth-child(3){width:18%}.doc-tabela th:nth-child(4){width:17%}.num{text-align:right}.linha-data td{background:#f8f9fa;font-weight:700;text-align:left}.doc-tabela tfoot th{background:#f8f9fa}.doc-observacoes{min-height:38px;border:1px solid #adb5bd;border-top:0;padding:7px;font-size:.68rem}.doc-objetivo{padding:8px 2px;font-size:.67rem}.doc-rodape{border-top:1px solid #343a40;display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:0}.doc-rodape>div{padding:6px;border-right:1px solid #adb5bd;font-size:.62rem;text-align:center}.doc-rodape>div:last-child{border-right:0}.doc-rodape-sistema{grid-column:1/-1;border-top:1px solid #adb5bd!important}.relatorio-modelo .btn{white-space:nowrap}@media print{@page{size:A4 landscape;margin:8mm}body{background:#fff!important}.relatorio-toolbar,.app-sidebar,.app-header,.app-footer,nav,.btn{display:none!important}.app-main,.container-fluid{margin:0!important;padding:0!important;width:100%!important;max-width:none!important}.modelo-documento{max-width:none;border:0;margin:0 0 8mm;box-shadow:none}.modelo-documento:not(:first-of-type){break-before:page}.doc-tabela tr{break-inside:avoid}.doc-cabecalho,.doc-rodape{-webkit-print-color-adjust:exact;print-color-adjust:exact}}
</style>
