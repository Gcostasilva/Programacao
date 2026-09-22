<?php

require_once __DIR__ . '/../../services/RelatorioProgramacaoService.php';

$anoAtual = (int) date('Y');
$mesAtual = (int) date('m');
$quinzenaAtual = date('j') <= 15 ? 1 : 2;
$quinzenaPadrao = sprintf('%04d-%02d-%d', $anoAtual, $mesAtual, $quinzenaAtual);

$quinzena = $_GET['quinzena'] ?? $quinzenaPadrao;
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

function rqQuinzenaLabel(string $quinzena): string
{
    $partes = explode('-', $quinzena);
    if (count($partes) !== 3) {
        return $quinzena;
    }
    return $partes[2] === '1' ? '1ª Quinzena' : '2ª Quinzena';
}

function rqValidarQuinzena(string $quinzena): bool
{
    return (bool) preg_match('/^\d{4}-(0[1-9]|1[0-2])-[12]$/', $quinzena);
}

try {
    if (!rqValidarQuinzena($quinzena)) {
        throw new InvalidArgumentException('Selecione uma quinzena válida.');
    }

    $relatorio = (new RelatorioProgramacaoService())->gerarQuinzenal($quinzena);
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
            <?php if ($relatorio && !empty($relatorio['itens'])): ?>
                <button type="button" class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir</button>
            <?php endif; ?>
        </div>
    </div>

    <form method="get" class="card shadow-sm mb-3 relatorio-toolbar">
        <input type="hidden" name="page" value="relatorio_programacao_quinzenal">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label class="form-label">Quinzena</label>
                    <input type="month" id="rqMes" class="form-control" value="<?= htmlspecialchars(substr($quinzena, 0, 7)) ?>">
                    <input type="hidden" name="quinzena" id="rqQuinzena" value="<?= htmlspecialchars($quinzena) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Período</label>
                    <select id="rqMetade" class="form-select">
                        <option value="1" <?= substr($quinzena, -1) === '1' ? 'selected' : '' ?>>1ª quinzena</option>
                        <option value="2" <?= substr($quinzena, -1) === '2' ? 'selected' : '' ?>>2ª quinzena</option>
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
    <?php elseif ($relatorio && empty($relatorio['itens'])): ?>
        <div class="alert alert-info relatorio-toolbar">Não há programação quinzenal cadastrada para o período selecionado.</div>
    <?php elseif ($relatorio): ?>
        <article class="modelo-documento">
            <header class="doc-cabecalho">
                <div class="doc-cabecalho-top">
                    <div class="doc-marca">
                        <strong>PERFINASA</strong>
                        <small>PERFILADOS DE AÇO</small>
                    </div>
                    <div class="doc-titulo">
                        <strong>SISTEMA DE GESTÃO DA QUALIDADE</strong>
                        <span>REGISTRO DE PROCESSO</span>
                    </div>
                    <div class="doc-codigo">
                        <strong>Identificação: RP 05</strong>
                        <span>Revisão: 04</span>
                    </div>
                </div>
                <div class="doc-titulo-principal">PROGRAMAÇÃO DE PRODUTO PADRÃO DE CORTE E DOBRA</div>
                <div class="doc-meta">
                    <span><strong>Data Prevista Inicial:</strong> <?= rqData($relatorio['periodo']['inicio']) ?></span>
                    <span><strong>Data Prevista Final:</strong> <?= rqData($relatorio['periodo']['fim']) ?></span>
                    <span><strong>Período:</strong> <?= htmlspecialchars(rqQuinzenaLabel($quinzena)) ?></span>
                </div>
            </header>

            <div class="doc-corpo">
                <div class="doc-secao-titulo">PROGRAMAÇÃO QUINZENAL</div>
                <table class="doc-tabela">
                    <thead>
                        <tr>
                            <th>CÓD</th>
                            <th>DESCRIÇÃO</th>
                            <th>QUANTIDADE DE PEÇAS</th>
                            <th>PRODUZIDO</th>
                            <th>SALDO</th>
                            <th>OP</th>
                            <th>PESO ESTIMADO (KG)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($relatorio['itens'] as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars((string) $item['produto_id']) ?></td>
                                <td><?= htmlspecialchars($item['descricao']) ?></td>
                                <td class="num"><?= rqNumero($item['quantidade']) ?></td>
                                <td class="num"><?= rqNumero($item['produzido']) ?></td>
                                <td class="num"><?= rqNumero($item['saldo']) ?></td>
                                <td><?= htmlspecialchars((string) ($item['ordem_producao'] ?? '')) ?></td>
                                <td class="num"><?= rqNumero($item['peso_estimado']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">TOTAL</th>
                            <th class="num"><?= rqNumero($relatorio['totais']['quantidade']) ?></th>
                            <th class="num"><?= rqNumero($relatorio['totais']['produzido']) ?></th>
                            <th class="num"><?= rqNumero($relatorio['totais']['saldo']) ?></th>
                            <th></th>
                            <th class="num"><?= rqNumero($relatorio['totais']['peso_estimado']) ?></th>
                        </tr>
                    </tfoot>
                </table>

                <div class="doc-observacoes">
                    <strong>Observações:</strong>
                    <?php foreach ($relatorio['itens'] as $item): ?>
                        <?php if (!empty($item['observacao'])): ?>
                            <div><strong><?= htmlspecialchars((string) $item['produto_id']) ?>:</strong> <?= htmlspecialchars($item['observacao']) ?></div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <div class="doc-resumo">
                    <div><strong>Total de itens:</strong> <?= count($relatorio['itens']) ?></div>
                    <div><strong>Total de peças:</strong> <?= rqNumero($relatorio['totais']['quantidade']) ?></div>
                    <div><strong>Peso estimado:</strong> <?= rqNumero($relatorio['totais']['peso_estimado']) ?> KG</div>
                </div>

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
    <?php endif; ?>
</div>

<script>
(function () {
    const mes = document.getElementById('rqMes');
    const metade = document.getElementById('rqMetade');
    const hidden = document.getElementById('rqQuinzena');
    if (!mes || !metade || !hidden) return;

    function atualizarQuinzena() {
        if (mes.value) hidden.value = mes.value + '-' + metade.value;
    }

    mes.addEventListener('change', atualizarQuinzena);
    metade.addEventListener('change', atualizarQuinzena);
})();
</script>

<style>
.relatorio-modelo{font-family:Arial,Helvetica,sans-serif;color:#212529}
.modelo-documento{max-width:1120px;margin:0 auto 20px;background:#fff;border:1px solid #343a40}
.doc-cabecalho{border-bottom:1px solid #343a40}
.doc-cabecalho-top{display:grid;grid-template-columns:1.15fr 2fr 1fr;min-height:62px;border-bottom:1px solid #343a40}
.doc-marca,.doc-titulo,.doc-codigo{padding:7px 10px;display:flex;flex-direction:column;justify-content:center}
.doc-marca{border-right:1px solid #343a40}.doc-marca strong{font-size:1.3rem}.doc-marca small{font-size:.55rem;letter-spacing:.1em}
.doc-titulo{text-align:center;font-size:.72rem;line-height:1.4}.doc-codigo{font-size:.68rem;line-height:1.5}
.doc-titulo-principal{text-align:center;font-weight:800;font-size:.9rem;padding:7px;border-bottom:1px solid #343a40}
.doc-meta{display:flex;justify-content:space-between;gap:12px;padding:6px 9px;font-size:.67rem}
.doc-corpo{padding:10px}.doc-secao-titulo{font-weight:800;font-size:.74rem;margin-bottom:5px}
.doc-tabela{width:100%;border-collapse:collapse;table-layout:fixed}
.doc-tabela th,.doc-tabela td{border:1px solid #adb5bd;padding:4px 5px;font-size:.67rem;line-height:1.2}
.doc-tabela th{background:#f1f3f5}.doc-tabela th:nth-child(1){width:11%}.doc-tabela th:nth-child(2){width:27%}.doc-tabela th:nth-child(3){width:14%}.doc-tabela th:nth-child(4){width:11%}.doc-tabela th:nth-child(5){width:10%}.doc-tabela th:nth-child(6){width:10%}.doc-tabela th:nth-child(7){width:17%}
.num{text-align:right}.doc-tabela tfoot th{background:#f8f9fa}
.doc-observacoes{min-height:42px;border:1px solid #adb5bd;border-top:0;padding:7px;font-size:.68rem}
.doc-resumo{display:flex;justify-content:space-between;border:1px solid #adb5bd;border-top:0;padding:7px;font-size:.68rem}
.doc-objetivo{padding:8px 2px;font-size:.67rem}
.doc-rodape{border-top:1px solid #343a40;display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:0}
.doc-rodape>div{padding:6px;border-right:1px solid #adb5bd;font-size:.62rem;text-align:center}.doc-rodape>div:last-child{border-right:0}
.doc-rodape-sistema{grid-column:1/-1;border-top:1px solid #adb5bd!important}
.relatorio-modelo .btn{white-space:nowrap}
@media print{
    @page{size:A4 landscape;margin:8mm}
    body{background:#fff!important}
    .relatorio-toolbar,.app-sidebar,.app-header,.app-footer,nav,.btn{display:none!important}
    .app-main,.container-fluid{margin:0!important;padding:0!important;width:100%!important;max-width:none!important}
    .modelo-documento{max-width:none;border:0;margin:0;box-shadow:none}
    .doc-tabela tr{break-inside:avoid}
    .doc-cabecalho,.doc-rodape{-webkit-print-color-adjust:exact;print-color-adjust:exact}
}
</style>
