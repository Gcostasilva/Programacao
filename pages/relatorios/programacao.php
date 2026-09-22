<?php
require_once __DIR__ . '/../../services/RelatorioProgramacaoService.php';
require_once __DIR__ . '/../../models/RecursoModel.php';

$semana = $_GET['semana'] ?? date('o-\\WW');
$recursoId = isset($_GET['recurso_id']) && $_GET['recurso_id'] !== '' ? filter_var($_GET['recurso_id'], FILTER_VALIDATE_INT) : null;
$recursos = (new RecursoModel())->listarSemanal();
$relatorio = null; $erro = null; $inicio = null; $fim = null;

function periodoDaSemana(string $semana): array {
    if (!preg_match('/^(\\d{4})-W(\\d{2})$/', $semana, $m)) throw new InvalidArgumentException('Informe uma semana válida.');
    $n = (int) $m[2]; if ($n < 1 || $n > 53) throw new InvalidArgumentException('Número de semana inválido.');
    $segunda = new DateTime(); $segunda->setISODate((int)$m[1], $n, 1); $sexta = clone $segunda; $sexta->modify('+4 days');
    return [$segunda->format('Y-m-d'), $sexta->format('Y-m-d')];
}
function rsData(string $data): string { return (new DateTime($data))->format('d/m/Y'); }
function rsNumero(float $valor): string { return number_format($valor, 0, ',', '.'); }
function rsDia(string $data): string { $map=['Sunday'=>'Domingo','Monday'=>'Segunda-feira','Tuesday'=>'Terça-feira','Wednesday'=>'Quarta-feira','Thursday'=>'Quinta-feira','Friday'=>'Sexta-feira','Saturday'=>'Sábado']; return $map[(new DateTime($data))->format('l')] ?? ''; }

try { [$inicio,$fim]=periodoDaSemana($semana); $relatorio=(new RelatorioProgramacaoService())->gerar($inicio,$fim,$recursoId); } catch(Throwable $e){$erro=$e->getMessage();}
?>

<div class="container-fluid py-3 rp04-page">
    <div class="d-flex justify-content-between align-items-center mb-3 rp04-toolbar">
        <div><h2 class="mb-1">Relatório de Programação — Semanal</h2><div class="text-muted small">Modelo RP 04 — Programação de produção</div></div>
        <div class="d-flex gap-2"><a href="?page=relatorios" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Relatórios</a><?php if($relatorio): ?><button type="button" class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir</button><?php endif; ?></div>
    </div>

    <form method="get" class="card shadow-sm mb-3 rp04-toolbar"><input type="hidden" name="page" value="relatorio_programacao"><div class="card-body"><div class="row g-3 align-items-end">
        <div class="col-md-3"><label class="form-label">Semana</label><input type="week" class="form-control" name="semana" value="<?=htmlspecialchars($semana)?>" required></div>
        <div class="col-md-5"><label class="form-label">Equipamento</label><select name="recurso_id" class="form-select"><option value="">Todos os equipamentos</option><?php foreach($recursos as $r): ?><option value="<?= (int)$r['id']?>" <?=$recursoId===(int)$r['id']?'selected':''?>><?=htmlspecialchars($r['descricao'])?></option><?php endforeach; ?></select></div>
        <div class="col-md-4"><button class="btn btn-primary w-100"><i class="bi bi-search"></i> Gerar relatório</button></div>
    </div></div></form>

    <?php if($erro): ?><div class="alert alert-danger rp04-toolbar"><?=htmlspecialchars($erro)?></div>
    <?php elseif($relatorio && empty($relatorio['equipamentos'])): ?><div class="alert alert-info rp04-toolbar">Não há programação para a semana e equipamento selecionados.</div>
    <?php elseif($relatorio): foreach($relatorio['equipamentos'] as $equipamento): ?>
        <article class="rp04-document">
            <header class="rp04-header">
                <div class="rp04-top"><div class="rp04-logo"><div class="rp04-logo-mark">P</div><div><strong>PERFINASA</strong><small>PERFILADOS DE AÇO</small></div></div><div class="rp04-system"><strong>Sistema de Gestão da Qualidade</strong><span>REGISTRO DE PRODUÇÃO</span></div><div class="rp04-code"><strong>Identificação: RP 04</strong><span>Revisão: 04</span></div></div>
                <div class="rp04-title">PROGRAMAÇÃO DE PRODUÇÃO</div>
                <div class="rp04-date"><span><strong>Data Prevista Inicial:</strong> <?=rsData($relatorio['periodo']['inicio'])?></span><span><strong>Data Prevista Final:</strong> <?=rsData($relatorio['periodo']['fim'])?></span><span><strong>Equipamento:</strong> <?=htmlspecialchars($equipamento['nome'])?></span></div>
            </header>

            <section class="rp04-responsaveis">
                <div><strong>Programação:</strong> Analista de PCP</div>
                <div><strong>Análise:</strong> Supervisor de Produção</div>
                <div><strong>Aprovação:</strong> Gerente Industrial</div>
                <div><strong>Responsável:</strong> ENCARREGADO DE PRODUÇÃO</div>
            </section>

            <section class="rp04-content">
                <?php foreach($equipamento['dias'] as $dia): ?>
                    <div class="rp04-day"><div class="rp04-day-title"><strong><?=htmlspecialchars(rsDia($dia['data']))?></strong><span><?=rsData($dia['data'])?></span><span>Total: <?=rsNumero((float)$dia['totais']['peso_estimado'])?> kg</span></div>
                    <table class="rp04-table"><thead><tr><th>CÓD</th><th>DESCRIÇÃO</th><th>DEMANDA</th><th>QUANTIDADE DE PEÇAS</th><th>PESO ESTIMADO (KG)</th></tr></thead><tbody>
                    <?php foreach($dia['itens'] as $item): ?><tr><td><?=htmlspecialchars((string)$item['produto_id'])?></td><td><?=htmlspecialchars($item['descricao'])?><?php if(!empty($item['observacao'])):?><small>Obs.: <?=htmlspecialchars($item['observacao'])?></small><?php endif;?></td><td><?=htmlspecialchars((string)($item['demanda']??''))?></td><td class="num"><?=rsNumero((float)$item['quantidade'])?></td><td class="num"><?=rsNumero((float)$item['peso_estimado'])?></td></tr><?php endforeach; ?>
                    </tbody></table></div>
                <?php endforeach; ?>
            </section>

            <section class="rp04-observacoes"><div><strong>Observações:</strong></div><div class="rp04-obs-area"><?php foreach($equipamento['dias'] as $dia): foreach($dia['itens'] as $item): if(!empty($item['observacao'])):?><div><strong><?=htmlspecialchars((string)$item['produto_id'])?>:</strong> <?=htmlspecialchars($item['observacao'])?></div><?php endif; endforeach; endforeach;?></div></section>
            <section class="rp04-storage"><table><thead><tr><th>Armazenamento</th><th>Preservação</th><th>Recuperação</th><th>Retenção</th><th>Disposição</th></tr></thead><tbody><tr><td>Pasta Produção</td><td>Back up/TI</td><td>Por data</td><td>03 anos</td><td>Deletar</td></tr></tbody></table></section>
        </article>
        <div class="rp04-print-footer"><span>SENADOR CANEDO, <?=date('d/m/Y')?></span><span>1/1</span></div>
    <?php endforeach; endif; ?>
</div>

<style>
.rp04-page{font-family:Arial,Helvetica,sans-serif;color:#111}.rp04-document{max-width:1120px;margin:0 auto;background:#fff;border:2px solid #111}.rp04-header{border-bottom:1px solid #111}.rp04-top{display:grid;grid-template-columns:1.05fr 2fr .95fr;min-height:55px;border-bottom:1px solid #111}.rp04-logo{display:flex;align-items:center;gap:7px;padding:4px 7px;border-right:1px solid #111}.rp04-logo-mark{width:38px;height:38px;display:flex;align-items:center;justify-content:center;background:#f15a24;color:#fff;font-weight:900;font-size:29px;border-right:10px solid #064b0b}.rp04-logo strong{display:block;font-size:1.35rem;line-height:1}.rp04-logo small{display:block;font-size:.48rem;letter-spacing:.09em;margin-top:2px}.rp04-system,.rp04-code{display:flex;flex-direction:column;align-items:center;justify-content:center;font-size:.72rem;line-height:1.3}.rp04-system{border-right:1px solid #111}.rp04-system strong{font-size:.8rem}.rp04-title{text-align:center;font-weight:800;font-size:.88rem;padding:6px;border-bottom:1px solid #111}.rp04-date{display:grid;grid-template-columns:1fr 1fr 1fr;font-size:.68rem}.rp04-date span{padding:5px 7px}.rp04-date span+span{border-left:1px solid #111}.rp04-responsaveis{display:grid;grid-template-columns:repeat(4,1fr);border-bottom:1px solid #111}.rp04-responsaveis div{padding:5px 6px;font-size:.66rem;text-align:center;border-right:1px solid #111;min-height:28px;display:flex;align-items:center;justify-content:center}.rp04-responsaveis div:last-child{border-right:0}.rp04-content{padding:0}.rp04-day{break-inside:avoid}.rp04-day-title{display:grid;grid-template-columns:1fr auto 1fr;gap:8px;align-items:center;background:#f1f3f5;border-top:1px solid #111;border-bottom:1px solid #111;padding:4px 6px;font-size:.7rem}.rp04-day-title span:nth-child(2){text-align:center}.rp04-day-title span:last-child{text-align:right}.rp04-table{width:100%;border-collapse:collapse;table-layout:fixed}.rp04-table th,.rp04-table td{border:1px solid #111;padding:3px 5px;font-size:.65rem;line-height:1.2}.rp04-table th{height:29px;font-size:.66rem}.rp04-table th:nth-child(1){width:15%}.rp04-table th:nth-child(2){width:37%}.rp04-table th:nth-child(3){width:15%}.rp04-table th:nth-child(4){width:17%}.rp04-table th:nth-child(5){width:16%}.rp04-table td:first-child,.rp04-table th:first-child{text-align:center}.rp04-table small{display:block;color:#555;font-size:.57rem;margin-top:2px}.num{text-align:right}.rp04-observacoes{border-top:1px solid #111}.rp04-observacoes>div:first-child{padding:3px 5px;border-bottom:1px solid #111;font-size:.68rem}.rp04-obs-area{min-height:45px;padding:5px;font-size:.64rem}.rp04-storage table{width:100%;border-collapse:collapse}.rp04-storage th,.rp04-storage td{border:1px solid #111;text-align:center;padding:5px 4px;font-size:.64rem}.rp04-storage th{font-size:.68rem}.rp04-print-footer{max-width:1120px;margin:25px auto 80px;display:flex;justify-content:space-between;font:12px Arial,sans-serif}.rp04-toolbar .btn{white-space:nowrap}
@media print{@page{size:A4 landscape;margin:8mm}body{background:#fff!important}.rp04-toolbar,.app-sidebar,.app-header,.app-footer,nav,.btn{display:none!important}.app-main,.container-fluid,.rp04-page{margin:0!important;padding:0!important;width:100%!important;max-width:none!important}.rp04-document{max-width:none;margin:0;border:2px solid #111;box-shadow:none}.rp04-document:not(:first-of-type){break-before:page}.rp04-day,.rp04-table tr{break-inside:avoid}.rp04-print-footer{max-width:none;margin:15px 0 0;font-size:10px}.rp04-header,.rp04-responsaveis,.rp04-storage{-webkit-print-color-adjust:exact;print-color-adjust:exact}}
</style>
