<?php
require_once __DIR__ . '/../../services/RelatorioProgramacaoService.php';
require_once __DIR__ . '/../../models/RecursoModel.php';

$data = $_GET['data'] ?? date('Y-m-d');
$recursoId = isset($_GET['recurso_id']) && $_GET['recurso_id'] !== '' ? filter_var($_GET['recurso_id'], FILTER_VALIDATE_INT) : null;
$recursos = (new RecursoModel())->listarDiario();
$relatorio = null;
$erro = null;

function rdData(string $d): string { return (new DateTime($d))->format('d/m/Y'); }
function rdNumero(float $v): string { return number_format($v, 0, ',', '.'); }
function rdDia(string $d): string { $map=['Sunday'=>'Domingo','Monday'=>'Segunda-feira','Tuesday'=>'Terça-feira','Wednesday'=>'Quarta-feira','Thursday'=>'Quinta-feira','Friday'=>'Sexta-feira','Saturday'=>'Sábado']; return $map[(new DateTime($d))->format('l')] ?? ''; }

try {
    $valida = DateTime::createFromFormat('Y-m-d', $data);
    if (!$valida || $valida->format('Y-m-d') !== $data) throw new InvalidArgumentException('Informe uma data válida.');
    $relatorio = (new RelatorioProgramacaoService())->gerar($data, $data, $recursoId);
} catch (Throwable $e) { $erro = $e->getMessage(); }
?>
<div class="container-fluid py-3 relatorio-modelo">
    <div class="d-flex justify-content-between align-items-center mb-3 relatorio-toolbar">
        <div><h2 class="mb-1">Relatório de Programação — Diária</h2><div class="text-muted small">Modelo RP 09 — programação de produção</div></div>
        <div class="d-flex gap-2"><a href="?page=relatorios" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Relatórios</a><?php if($relatorio && !empty($relatorio['equipamentos'])): ?><button type="button" class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir</button><?php endif; ?></div>
    </div>
    <form method="get" class="card shadow-sm mb-3 relatorio-toolbar"><input type="hidden" name="page" value="relatorio_programacao_diaria"><div class="card-body"><div class="row g-3 align-items-end"><div class="col-md-3"><label class="form-label">Data</label><input type="date" name="data" class="form-control" value="<?=htmlspecialchars($data)?>" required></div><div class="col-md-5"><label class="form-label">Equipamento</label><select name="recurso_id" class="form-select"><option value="">Todos os equipamentos</option><?php foreach($recursos as $r): ?><option value="<?= (int)$r['id']?>" <?=$recursoId===(int)$r['id']?'selected':''?>><?=htmlspecialchars($r['descricao'])?></option><?php endforeach; ?></select></div><div class="col-md-4"><button class="btn btn-primary w-100"><i class="bi bi-search"></i> Gerar relatório</button></div></div></div></form>
    <?php if($erro): ?><div class="alert alert-danger relatorio-toolbar"><?=htmlspecialchars($erro)?></div>
    <?php elseif($relatorio && empty($relatorio['equipamentos'])): ?><div class="alert alert-info relatorio-toolbar">Não há programação para a data e equipamento selecionados.</div>
    <?php elseif($relatorio): foreach($relatorio['equipamentos'] as $equipamento): $dia=$equipamento['dias'][0]??null; if(!$dia) continue; ?>
    <article class="modelo-documento">
        <header class="doc-cabecalho"><div class="doc-cabecalho-top"><div class="doc-marca"><strong>PERFINASA</strong><small>PERFILADOS DE AÇO</small></div><div class="doc-titulo"><strong>SISTEMA DE GESTÃO DA QUALIDADE</strong><span>REGISTRO DE PRODUÇÃO</span></div><div class="doc-codigo"><strong>CÓD.: RP 09</strong><span>Revisão: 04</span><span>PAG 1</span></div></div><div class="doc-titulo-principal">PROGRAMAÇÃO DE PRODUÇÃO</div><div class="doc-meta"><span><strong>Equipamento:</strong> <?=htmlspecialchars($equipamento['nome'])?></span><span><strong>Data Inicial:</strong> <?=rdData($data)?></span><span><strong>Data Final:</strong> <?=rdData($data)?></span><span><strong>Responsável:</strong> ENC. DE PRODUÇÃO</span></div></header>
        <div class="doc-corpo"><div class="doc-info">Programação de produção — <?=rdDia($data)?>, <?=rdData($data)?></div><table class="doc-tabela"><thead><tr><th>Nº DO PEDIDO / DEMANDA</th><th>ESPESSURA / PRODUTO</th><th>PESO (KG)</th><th>QUANTIDADE</th></tr></thead><tbody><?php foreach($dia['itens'] as $item): ?><tr><td><?=htmlspecialchars((string)($item['demanda']??''))?></td><td><?=htmlspecialchars($item['descricao'])?></td><td class="num"><?=rdNumero((float)$item['peso_estimado'])?></td><td class="num"><?=rdNumero((float)$item['quantidade'])?></td></tr><?php endforeach; ?></tbody><tfoot><tr><th colspan="2">Peso Total</th><th class="num"><?=rdNumero((float)$equipamento['totais']['peso_estimado'])?></th><th class="num"><?=rdNumero((float)$equipamento['totais']['quantidade'])?></th></tr></tfoot></table><div class="doc-observacoes"><strong>Observação:</strong><?php foreach($dia['itens'] as $item): if(!empty($item['observacao'])): ?><div><?=htmlspecialchars($item['observacao'])?></div><?php endif; endforeach; ?></div></div>
        <footer class="doc-rodape"><div><strong>Programado:</strong> ANALISTA DE PCP</div><div><strong>Analisado:</strong> SUPERVISOR DE PRODUÇÃO</div><div><strong>Aprovado:</strong> GERENTE INDUSTRIAL</div><div class="doc-rodape-sistema">Sistema de Gestão da Qualidade — REGISTRO DE PRODUÇÃO — CÓD.: RP 09</div></footer>
    </article>
    <?php endforeach; endif; ?>
</div>
<style>
.relatorio-modelo{font-family:Arial,Helvetica,sans-serif;color:#212529}.modelo-documento{max-width:1120px;margin:0 auto 20px;background:#fff;border:1px solid #343a40}.doc-cabecalho{border-bottom:1px solid #343a40}.doc-cabecalho-top{display:grid;grid-template-columns:1.15fr 2fr 1fr;min-height:62px;border-bottom:1px solid #343a40}.doc-marca,.doc-titulo,.doc-codigo{padding:7px 10px;display:flex;flex-direction:column;justify-content:center}.doc-marca{border-right:1px solid #343a40}.doc-marca strong{font-size:1.3rem}.doc-marca small{font-size:.55rem;letter-spacing:.1em}.doc-titulo{text-align:center;font-size:.72rem;line-height:1.4}.doc-codigo{font-size:.68rem;line-height:1.5}.doc-titulo-principal{text-align:center;font-weight:800;font-size:.9rem;padding:7px;border-bottom:1px solid #343a40}.doc-meta{display:flex;justify-content:space-between;gap:12px;padding:6px 9px;font-size:.67rem}.doc-corpo{padding:10px}.doc-info{font-size:.7rem;font-weight:700;margin-bottom:6px}.doc-tabela{width:100%;border-collapse:collapse;table-layout:fixed}.doc-tabela th,.doc-tabela td{border:1px solid #adb5bd;padding:5px;font-size:.68rem}.doc-tabela th{background:#f1f3f5}.doc-tabela th:first-child{width:23%}.doc-tabela th:nth-child(3){width:16%}.doc-tabela th:nth-child(4){width:15%}.num{text-align:right}.doc-tabela tfoot th{background:#f8f9fa}.doc-observacoes{min-height:42px;border:1px solid #adb5bd;border-top:0;padding:7px;font-size:.68rem}.doc-rodape{border-top:1px solid #343a40;display:grid;grid-template-columns:1fr 1fr 1fr;gap:0}.doc-rodape>div{padding:7px;border-right:1px solid #adb5bd;font-size:.62rem;text-align:center}.doc-rodape>div:nth-child(3){border-right:0}.doc-rodape-sistema{grid-column:1/-1;border-top:1px solid #adb5bd!important;border-right:0!important}.relatorio-modelo .btn{white-space:nowrap}@media print{@page{size:A4 landscape;margin:8mm}body{background:#fff!important}.relatorio-toolbar,.app-sidebar,.app-header,.app-footer,nav,.btn{display:none!important}.app-main,.container-fluid{margin:0!important;padding:0!important;width:100%!important;max-width:none!important}.modelo-documento{max-width:none;border:0;margin:0}.doc-tabela tr{break-inside:avoid}.doc-cabecalho,.doc-rodape{-webkit-print-color-adjust:exact;print-color-adjust:exact}}
</style>
