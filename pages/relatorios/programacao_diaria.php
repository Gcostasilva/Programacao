<?php
require_once __DIR__ . '/../../services/RelatorioProgramacaoService.php';
require_once __DIR__ . '/../../models/RecursoModel.php';

$data = $_GET['data'] ?? date('Y-m-d');
$recursoId = isset($_GET['recurso_id']) && $_GET['recurso_id'] !== ''
    ? filter_var($_GET['recurso_id'], FILTER_VALIDATE_INT)
    : null;

$recursos = (new RecursoModel())->listarDiario();
$relatorio = null;
$erro = null;

function rdData(string $d): string { return (new DateTime($d))->format('d/m/Y'); }
function rdNumero(float $v): string { return number_format($v, 0, ',', '.'); }
function rdEspessura(float $v): string { return number_format($v, 2, ',', '.'); }

try {
    $valida = DateTime::createFromFormat('Y-m-d', $data);
    if (!$valida || $valida->format('Y-m-d') !== $data) {
        throw new InvalidArgumentException('Informe uma data válida.');
    }
    $relatorio = (new RelatorioProgramacaoService())->gerarDiaria($data, $recursoId);
} catch (Throwable $e) {
    $erro = $e->getMessage();
}
?>

<div class="container-fluid py-3 relatorio-modelo">
    <div class="d-flex justify-content-between align-items-center mb-3 relatorio-toolbar">
        <div>
            <h2 class="mb-1">Relatório de Programação — Diária</h2>
            <div class="text-muted small">Modelo RP 09 — Registro de Produção</div>
        </div>
        <div class="d-flex gap-2">
            <a href="?page=relatorios" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Relatórios</a>
            <?php if ($relatorio && !empty($relatorio['equipamentos'])): ?>
                <button type="button" class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir</button>
            <?php endif; ?>
        </div>
    </div>

    <form method="get" class="card shadow-sm mb-3 relatorio-toolbar">
        <input type="hidden" name="page" value="relatorio_programacao_diaria">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Data</label>
                    <input type="date" name="data" class="form-control" value="<?= htmlspecialchars($data) ?>" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Equipamento</label>
                    <select name="recurso_id" class="form-select">
                        <option value="">Todos os equipamentos</option>
                        <?php foreach ($recursos as $recurso): ?>
                            <option value="<?= (int)$recurso['id'] ?>" <?= $recursoId === (int)$recurso['id'] ? 'selected' : '' ?>><?= htmlspecialchars($recurso['descricao']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary w-100"><i class="bi bi-search"></i> Gerar relatório</button>
                </div>
            </div>
        </div>
    </form>

    <?php if ($erro): ?>
        <div class="alert alert-danger relatorio-toolbar"><?= htmlspecialchars($erro) ?></div>
    <?php elseif ($relatorio && empty($relatorio['equipamentos'])): ?>
        <div class="alert alert-info relatorio-toolbar">Não há programação para a data e equipamento selecionados.</div>
    <?php elseif ($relatorio): ?>
        <?php foreach ($relatorio['equipamentos'] as $equipamento): ?>
            <?php $itens = $equipamento['itens']; ?>
            <article class="rp09-page">
                <div class="rp09-document">
                    <header>
                        <div class="rp09-header-main">
                            <div class="rp09-logo-cell"><div class="rp09-logo-mark">P</div><div class="rp09-logo-name">PERFINASA</div></div>
                            <div class="rp09-quality"><strong>Sistema de Gestão da Qualidade</strong><strong>REGISTRO DE PRODUÇÃO</strong></div>
                            <div class="rp09-control">
                                <div><strong>CÓD.:</strong> RP 09</div>
                                <div class="rp09-control-grid"><span>PAG</span><span>Revisão</span><span>1</span><span>4</span></div>
                            </div>
                        </div>
                        <div class="rp09-title">Programação de produção.</div>
                        <div class="rp09-approval-row">
                            <div>Programado : <strong>ANALISTA DE PCP</strong></div>
                            <div>Analisado: <strong>SUPERVISOR DE PRODUÇÃO</strong></div>
                            <div>Aprovado: <strong>GERENTE INDUSTRIAL</strong></div>
                        </div>
                        <div class="rp09-info-row">
                            <div>Equipamento: <strong><?= htmlspecialchars($equipamento['nome']) ?></strong></div>
                            <div>Data Inicial: <strong><?= rdData($data) ?></strong></div>
                            <div>Data Final: <strong><?= rdData($data) ?></strong></div>
                            <div>Responsável: <strong>ENC. DE PRODUÇÃO</strong></div>
                        </div>
                    </header>

                    <section class="rp09-production">
                        <table class="rp09-table">
                            <colgroup><col class="col-pedido"><col class="col-vendedor"><col class="col-espessura"><col class="col-aco"><col class="col-peso"></colgroup>
                            <thead><tr><th>Nº do pedido</th><th>Vendedor</th><th>Espessura do produto (mm)</th><th>Aço</th><th>PESO (KG)</th></tr></thead>
                            <tbody>
                                <?php foreach ($itens as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars((string)($item['pedido'] ?? '')) ?></td>
                                        <td><?= htmlspecialchars((string)($item['vendedor'] ?? '')) ?></td>
                                        <td><?= rdEspessura((float)($item['espessura'] ?? 0)) ?></td>
                                        <td><?= htmlspecialchars((string)($item['aco'] ?? '')) ?></td>
                                        <td class="rp09-number"><?= rdNumero((float)$item['peso']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php for ($i = count($itens); $i < 4; $i++): ?>
                                    <tr class="rp09-empty-row"><td></td><td></td><td></td><td></td><td></td></tr>
                                <?php endfor; ?>
                            </tbody>
                            <tfoot><tr><td colspan="4" class="rp09-total-label">Peso Total:</td><td class="rp09-number"><?= rdNumero((float)$equipamento['totais']['peso']) ?></td></tr></tfoot>
                        </table>

                        <div class="rp09-observation-title">Observação:</div>
                        <div class="rp09-observation-body">
                            <?php foreach ($itens as $item): if (!empty($item['observacao'])): ?>
                                <div><?= htmlspecialchars($item['observacao']) ?></div>
                            <?php endif; endforeach; ?>
                        </div>
                    </section>

                    <section class="rp09-retention">
                        <div>Armazenagem</div><div>Disposição</div><div>Proteção</div><div>Recuperação</div><div>Retenção</div>
                        <div>PRODUÇÃO</div><div>LIXO</div><div>PASTA/ELETRÔNICO</div><div>DATA</div><div>1 ANO</div>
                    </section>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
.relatorio-modelo{color:#212529;font-family:Arial,Helvetica,sans-serif}.rp09-page{width:100%;max-width:1180px;min-height:190mm;margin:0 auto 24px;display:flex;flex-direction:column;background:#fff}.rp09-document{width:100%;border:2px solid #111;background:#fff}.rp09-header-main{display:grid;grid-template-columns:26% 48% 26%;height:76px;background:#d9d9d9;border-bottom:1px solid #111}.rp09-logo-cell,.rp09-quality,.rp09-control{min-width:0;display:flex;align-items:center}.rp09-logo-cell{gap:7px;padding:10px;border-right:1px solid #111}.rp09-logo-mark{width:39px;height:39px;flex:0 0 39px;display:grid;place-items:center;background:#f4511e;color:#fff;font-size:29px;font-weight:900;font-style:italic}.rp09-logo-name{padding:5px 10px;background:#064d12;color:#fff;font-size:20px;font-weight:800;line-height:1}.rp09-quality{flex-direction:column;justify-content:center;gap:4px;text-align:center;border-right:1px solid #111;font-size:14px;line-height:1.15}.rp09-control{flex-direction:column;align-items:stretch;justify-content:flex-start;font-size:13px;text-align:center}.rp09-control>div:first-child{height:31px;display:flex;align-items:center;justify-content:center;border-bottom:1px solid #111}.rp09-control-grid{display:grid;grid-template-columns:1fr 1fr;grid-template-rows:22px 22px;height:44px}.rp09-control-grid span{display:flex;align-items:center;justify-content:center;border-right:1px solid #111;border-bottom:1px solid #111}.rp09-control-grid span:nth-child(2n){border-right:0}.rp09-control-grid span:nth-child(n+3){border-bottom:0}.rp09-title{height:28px;display:flex;align-items:center;justify-content:center;background:#d9d9d9;border-bottom:1px solid #111;font-size:14px;font-weight:700}.rp09-approval-row,.rp09-info-row{display:grid;background:#d9d9d9;border-bottom:1px solid #111;font-size:12px;line-height:1.15}.rp09-approval-row{grid-template-columns:1fr 1.35fr 1fr}.rp09-info-row{grid-template-columns:1.15fr 1fr 1fr 1.15fr}.rp09-approval-row>div,.rp09-info-row>div{min-width:0;min-height:24px;display:flex;align-items:center;justify-content:center;padding:3px 5px;text-align:center;border-right:1px solid #111;white-space:nowrap}.rp09-approval-row>div:last-child,.rp09-info-row>div:last-child{border-right:0}.rp09-table{width:100%;border-collapse:collapse;table-layout:fixed}.rp09-table th,.rp09-table td{border-right:1px solid #111;border-bottom:1px solid #111;padding:3px 5px;height:22px;font-size:12px;line-height:1.15;text-align:center;vertical-align:middle}.rp09-table th:last-child,.rp09-table td:last-child{border-right:0}.rp09-table thead th{height:25px;background:#d9d9d9;font-weight:700}.rp09-table .col-pedido{width:13%}.rp09-table .col-vendedor{width:12%}.rp09-table .col-espessura{width:25%}.rp09-table .col-aco{width:25%}.rp09-table .col-peso{width:25%}.rp09-empty-row td{height:22px}.rp09-number{text-align:right!important;padding-right:9px!important}.rp09-total-label{height:28px!important;text-align:right!important;padding-right:8px!important}.rp09-observation-title{height:25px;display:flex;align-items:center;padding:0 4px;background:#d9d9d9;border-bottom:1px solid #111;font-size:12px}.rp09-observation-body{height:45px;padding:5px;font-size:11px;border-bottom:1px solid #111;overflow:hidden}.rp09-retention{display:grid;grid-template-columns:13% 12% 25% 25% 25%;background:#d9d9d9;font-size:12px;text-align:center}.rp09-retention>div{min-height:27px;display:flex;align-items:center;justify-content:center;padding:4px;border-right:1px solid #111;border-bottom:1px solid #111}.rp09-retention>div:nth-child(5n){border-right:0}.rp09-retention>div:nth-child(n+6){border-bottom:0;font-weight:500}.rp09-page-footer{position:relative;min-height:36mm;font-size:12px;display:flex;align-items:flex-end;justify-content:center;padding-bottom:4mm}.rp09-page-footer span:last-child{position:absolute;right:2px;bottom:4mm}.relatorio-modelo .btn{white-space:nowrap}
@media print{@page{size:A4 landscape;margin:8mm 8mm 6mm}body{background:#fff!important}.relatorio-toolbar,.app-sidebar,.app-header,.app-footer,nav,.btn{display:none!important}.app-main,.container-fluid{margin:0!important;padding:0!important;width:100%!important;max-width:none!important}.rp09-page{max-width:none;min-height:0;margin:0;break-after:page}.rp09-page:last-child{break-after:auto}.rp09-document{border:2px solid #111}.rp09-page-footer{min-height:28mm}.rp09-table tr{break-inside:avoid}.rp09-header-main,.rp09-title,.rp09-approval-row,.rp09-info-row,.rp09-table thead th,.rp09-observation-title,.rp09-retention{-webkit-print-color-adjust:exact;print-color-adjust:exact}}
</style>
