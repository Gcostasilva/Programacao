<?php
require_once __DIR__ . '/../../services/RelatorioProgramacaoService.php';

$anoAtual = (int) date('Y');
$mesAtual = (int) date('m');
$quinzenaAtual = date('j') <= 15 ? 1 : 2;
$quinzenaPadrao = sprintf('%04d-%02d-%d', $anoAtual, $mesAtual, $quinzenaAtual);
$quinzena = $_GET['quinzena'] ?? $quinzenaPadrao;
$relatorio = null;
$erro = null;

function rqData(string $data): string { return (new DateTime($data))->format('d/m/Y'); }
function rqNumero(float $valor): string { return number_format($valor, 0, ',', '.'); }
function rqQuinzenaLabel(string $quinzena): string {
    $partes = explode('-', $quinzena);
    return count($partes) === 3 && $partes[2] === '1' ? '1ª Quinzena' : '2ª Quinzena';
}
function rqValidarQuinzena(string $quinzena): bool { return (bool) preg_match('/^\d{4}-(0[1-9]|1[0-2])-[12]$/', $quinzena); }

try {
    if (!rqValidarQuinzena($quinzena)) throw new InvalidArgumentException('Selecione uma quinzena válida.');
    $relatorio = (new RelatorioProgramacaoService())->gerarQuinzenal($quinzena);
} catch (Throwable $e) { $erro = $e->getMessage(); }
?>

<div class="container-fluid py-3 rp05-page">
    <div class="d-flex justify-content-between align-items-center mb-3 rp05-toolbar">
        <div><h2 class="mb-1">Relatório de Programação — Quinzenal</h2><div class="text-muted small">Modelo RP 05 — Programação de produto padrão de Corte e Dobra</div></div>
        <div class="d-flex gap-2"><a href="?page=relatorios" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Relatórios</a><?php if ($relatorio && !empty($relatorio['itens'])): ?><button type="button" class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir</button><?php endif; ?></div>
    </div>

    <form method="get" class="card shadow-sm mb-3 rp05-toolbar">
        <input type="hidden" name="page" value="relatorio_programacao_quinzenal">
        <div class="card-body"><div class="row g-3 align-items-end">
            <div class="col-md-7"><label class="form-label">Mês</label><input type="month" id="rp05Mes" class="form-control" value="<?= htmlspecialchars(substr($quinzena, 0, 7)) ?>"></div>
            <div class="col-md-3"><label class="form-label">Período</label><select id="rp05Metade" class="form-select"><option value="1" <?= substr($quinzena, -1) === '1' ? 'selected' : '' ?>>1ª quinzena</option><option value="2" <?= substr($quinzena, -1) === '2' ? 'selected' : '' ?>>2ª quinzena</option></select></div>
            <div class="col-md-2"><button class="btn btn-primary w-100"><i class="bi bi-search"></i> Gerar</button></div>
            <input type="hidden" name="quinzena" id="rp05Quinzena" value="<?= htmlspecialchars($quinzena) ?>">
        </div></div>
    </form>

    <?php if ($erro): ?>
        <div class="alert alert-danger rp05-toolbar"><?= htmlspecialchars($erro) ?></div>
    <?php elseif ($relatorio && empty($relatorio['itens'])): ?>
        <div class="alert alert-info rp05-toolbar">Não há programação quinzenal cadastrada para o período selecionado.</div>
    <?php elseif ($relatorio): ?>
        <article class="rp05-document">
            <header class="rp05-header">
                <div class="rp05-header-grid rp05-header-top">
                    <div class="rp05-logo"><div class="rp05-logo-mark">P</div><div><strong>PERFINASA</strong><small>PERFILADOS DE AÇO</small></div></div>
                    <div class="rp05-cell rp05-system"><strong>Sistema de Gestão da Qualidade</strong><span>REGISTRO DE PROCESSO</span></div>
                    <div class="rp05-cell rp05-ident"><strong>Identificação: RP 05</strong><span>Liberado em</span></div>
                </div>
                <div class="rp05-header-grid rp05-header-mid">
                    <div class="rp05-cell rp05-empty"></div><div class="rp05-cell rp05-liberado">04/11/2020</div><div class="rp05-cell rp05-versao"><span>Versão</span><strong>04</strong></div>
                </div>
                <div class="rp05-title">Programação de produto padrão de Corte e Dobra</div>
            </header>

            <section class="rp05-meta">
                <div class="rp05-line"><strong>Objetivo:</strong></div>
                <div class="rp05-line">Registrar a programação de produção do produto padrão para um determinado período.</div>
                <div class="rp05-line"><strong>Programação:</strong> Analista de PCP</div>
                <div class="rp05-line"><strong>Análise:</strong> Supervisor de Produção</div>
                <div class="rp05-line"><strong>Aprovação:</strong> Gerente Industrial</div>
                <div class="rp05-line"><strong>Responsável:</strong> ENCARREGADO DE PRODUÇÃO</div>
                <div class="rp05-dates"><span><strong>Data Prevista Inicial:</strong> <?= rqData($relatorio['periodo']['inicio']) ?></span><span><strong>Data Prevista Final:</strong> <?= rqData($relatorio['periodo']['fim']) ?></span></div>
            </section>

            <section class="rp05-table-wrap">
                <table class="rp05-table">
                    <thead><tr><th>CÓD</th><th>DESCRIÇÃO</th><th>QUANTIDADE DE PEÇAS</th></tr></thead>
                    <tbody>
                    <?php foreach ($relatorio['itens'] as $item): ?>
                        <tr><td><?= htmlspecialchars((string) $item['produto_id']) ?></td><td><?= htmlspecialchars($item['descricao']) ?></td><td class="num"><?= rqNumero($item['quantidade']) ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <section class="rp05-observacoes"><div class="rp05-section-label">Observações:</div><div class="rp05-observacoes-area">
                <?php foreach ($relatorio['itens'] as $item): if (!empty($item['observacao'])): ?><div><strong><?= htmlspecialchars((string) $item['produto_id']) ?>:</strong> <?= htmlspecialchars($item['observacao']) ?></div><?php endif; endforeach; ?>
            </div></section>

            <section class="rp05-storage">
                <table><thead><tr><th>Armazenamento</th><th>Preservação</th><th>Recuperação</th><th>Retenção</th><th>Disposição</th></tr></thead><tbody><tr><td>Pasta Produção</td><td>Back up/TI</td><td>Por data</td><td>03 anos</td><td>Deletar</td></tr></tbody></table>
            </section>
        </article>
        <div class="rp05-print-footer"><span>SENADOR CANEDO, <?= date('d/m/Y') ?></span><span>1/1</span></div>
    <?php endif; ?>
</div>

<script>
(() => { const mes=document.getElementById('rp05Mes'), metade=document.getElementById('rp05Metade'), hidden=document.getElementById('rp05Quinzena'); if(!mes||!metade||!hidden)return; const sync=()=>{if(mes.value)hidden.value=mes.value+'-'+metade.value}; mes.addEventListener('change',sync); metade.addEventListener('change',sync); })();
</script>

<style>
.rp05-page{font-family:Arial,Helvetica,sans-serif;color:#111}.rp05-document{width:100%;max-width:1120px;margin:0 auto;background:#fff;border:2px solid #111}.rp05-header{border-bottom:1px solid #111}.rp05-header-grid{display:grid;grid-template-columns:1.05fr 2fr .95fr}.rp05-header-top{min-height:52px}.rp05-logo{display:flex;align-items:center;gap:7px;padding:4px 7px;border-right:1px solid #111}.rp05-logo-mark{width:38px;height:38px;display:flex;align-items:center;justify-content:center;background:#f15a24;color:#fff;font-weight:900;font-size:29px;border-right:10px solid #064b0b}.rp05-logo strong{display:block;font-size:1.35rem;line-height:1}.rp05-logo small{display:block;font-size:.48rem;letter-spacing:.09em;margin-top:2px}.rp05-cell{display:flex;align-items:center;justify-content:center;border-right:1px solid #111;padding:3px 6px;font-size:.72rem}.rp05-cell:last-child{border-right:0}.rp05-system{flex-direction:column;line-height:1.2}.rp05-system strong{font-size:.78rem}.rp05-system span{font-size:.68rem}.rp05-ident{flex-direction:column;font-size:.72rem}.rp05-header-mid{min-height:27px;border-top:1px solid #111}.rp05-empty{border-right:1px solid #111}.rp05-liberado{font-weight:700}.rp05-versao{justify-content:space-between;padding:3px 8px}.rp05-title{text-align:center;font-weight:800;font-size:.82rem;padding:5px;border-top:1px solid #111}.rp05-meta{border-bottom:1px solid #111}.rp05-line{min-height:24px;padding:3px 6px;border-top:1px solid #777;font-size:.7rem}.rp05-line:first-child{border-top:0}.rp05-dates{display:grid;grid-template-columns:1fr 1fr;border-top:1px solid #111;font-size:.7rem}.rp05-dates span{padding:4px 6px}.rp05-dates span+span{border-left:1px solid #111}.rp05-table{width:100%;border-collapse:collapse;table-layout:fixed}.rp05-table th,.rp05-table td{border:1px solid #111;padding:3px 5px;font-size:.68rem;height:21px}.rp05-table th{height:40px;text-align:center;font-size:.74rem}.rp05-table th:nth-child(1){width:18%}.rp05-table th:nth-child(2){width:49%}.rp05-table th:nth-child(3){width:33%}.rp05-table td:first-child{text-align:center}.rp05-table td:nth-child(2){white-space:nowrap}.num{text-align:center}.rp05-section-label{border-top:1px solid #111;border-bottom:1px solid #111;padding:3px 5px;font-size:.68rem}.rp05-observacoes-area{min-height:74px;padding:5px;font-size:.67rem;border-bottom:1px solid #111}.rp05-storage table{width:100%;border-collapse:collapse;table-layout:fixed}.rp05-storage th,.rp05-storage td{border:1px solid #111;padding:5px 4px;text-align:center;font-size:.66rem}.rp05-storage th{font-size:.7rem;height:28px}.rp05-print-footer{max-width:1120px;margin:125px auto 0;display:flex;justify-content:space-between;font:14px Arial,sans-serif}.rp05-toolbar .btn{white-space:nowrap}
@media print{ @page{size:A4 portrait;margin:8mm 8mm 10mm} body{background:#fff!important}.rp05-toolbar,.app-sidebar,.app-header,.app-footer,nav,.btn{display:none!important}.app-main,.container-fluid,.rp05-page{margin:0!important;padding:0!important;width:100%!important;max-width:none!important}.rp05-document{max-width:none;border:2px solid #111;margin:0;box-shadow:none}.rp05-print-footer{max-width:none;margin:118px 0 0;font-size:11px}.rp05-table tr{break-inside:avoid}.rp05-header,.rp05-storage{-webkit-print-color-adjust:exact;print-color-adjust:exact}}
</style>
