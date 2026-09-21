<?php

require_once __DIR__ . '/../../services/RelatorioProgramacaoService.php';
require_once __DIR__ . '/../../models/RecursoModel.php';

$semana = $_GET['semana'] ?? date('o-\\WW');
$recursoId = isset($_GET['recurso_id']) && $_GET['recurso_id'] !== ''
    ? filter_var($_GET['recurso_id'], FILTER_VALIDATE_INT)
    : null;

$recursoModel = new RecursoModel();
$recursos = $recursoModel->listarSemanal();

$relatorio = null;
$erro = null;
$inicio = null;
$fim = null;

/**
 * Converte uma semana ISO (YYYY-Www) no intervalo de segunda a sexta.
 */
function periodoDaSemana(string $semana): array
{
    if (!preg_match('/^(\\d{4})-W(\\d{2})$/', $semana, $matches)) {
        throw new InvalidArgumentException('Informe uma semana válida.');
    }

    $ano = (int) $matches[1];
    $numeroSemana = (int) $matches[2];

    if ($numeroSemana < 1 || $numeroSemana > 53) {
        throw new InvalidArgumentException('Número de semana inválido.');
    }

    $segunda = new DateTime();
    $segunda->setISODate($ano, $numeroSemana, 1);

    // A programação semanal considera segunda a sexta-feira.
    $sexta = clone $segunda;
    $sexta->modify('+4 days');

    return [$segunda->format('Y-m-d'), $sexta->format('Y-m-d')];
}

try {
    [$inicio, $fim] = periodoDaSemana($semana);

    $service = new RelatorioProgramacaoService();
    $relatorio = $service->gerar($inicio, $fim, $recursoId);
} catch (Throwable $e) {
    $erro = $e->getMessage();
}

function formatarDataRelatorio(string $data): string
{
    return (new DateTime($data))->format('d/m/Y');
}

function formatarNumeroRelatorio(float $valor): string
{
    return number_format($valor, 2, ',', '.');
}

function nomeDiaRelatorio(string $data): string
{
    $dias = [
        'Sunday' => 'Domingo',
        'Monday' => 'Segunda-feira',
        'Tuesday' => 'Terça-feira',
        'Wednesday' => 'Quarta-feira',
        'Thursday' => 'Quinta-feira',
        'Friday' => 'Sexta-feira',
        'Saturday' => 'Sábado',
    ];

    return $dias[(new DateTime($data))->format('l')] ?? '';
}
?>

<div class="container-fluid py-3 report-page">
    <div class="d-flex justify-content-between align-items-center mb-3 report-toolbar">
        <div>
            <h2 class="mb-1">Relatório de Programação</h2>
            <div class="text-muted small">Programação planejada de produção</div>
        </div>
        <div class="d-flex gap-2">
            <a href="?page=relatorios" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Relatórios
            </a>
            <?php if ($relatorio): ?>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="bi bi-printer"></i> Imprimir
                </button>
            <?php endif; ?>
        </div>
    </div>

    <form method="get" class="card shadow-sm mb-3 report-filter report-toolbar">
        <input type="hidden" name="page" value="relatorio_programacao">
        <div class="card-body py-2">
            <div class="row g-2 align-items-end">
                <div class="col-md-3 col-lg-2">
                    <label class="form-label mb-1">Semana</label>
                    <input type="week" name="semana" class="form-control" value="<?= htmlspecialchars($semana) ?>" required>
                </div>
                <div class="col-md-5 col-lg-4">
                    <label class="form-label mb-1">Equipamento</label>
                    <select name="recurso_id" class="form-select">
                        <option value="">Todos os equipamentos</option>
                        <?php foreach ($recursos as $recurso): ?>
                            <option value="<?= (int) $recurso['id'] ?>" <?= $recursoId === (int) $recurso['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($recurso['descricao']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 col-lg-2">
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Gerar relatório
                    </button>
                </div>
                <?php if ($inicio && $fim): ?>
                    <div class="col-lg-4 d-none d-lg-block text-end text-muted small pb-1">
                        <?= formatarDataRelatorio($inicio) ?> a <?= formatarDataRelatorio($fim) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </form>

    <?php if ($erro): ?>
        <div class="alert alert-danger report-toolbar">
            <?= htmlspecialchars($erro) ?>
        </div>
    <?php elseif ($relatorio): ?>
        <div class="report-preview bg-white border shadow-sm">
            <header class="report-header">
                <div class="report-header-top">
                    <div class="report-brand">
                        <div class="report-brand-name">PERFINASA</div>
                        <div class="report-brand-subtitle">PERFILADOS DE AÇO</div>
                    </div>
                    <div class="report-document-title">
                        <div class="fw-bold">SISTEMA DE GESTÃO DA QUALIDADE</div>
                        <div>REGISTRO DE PRODUÇÃO</div>
                    </div>
                    <div class="report-document-code">
                        <div><strong>Código:</strong> RP 04</div>
                        <div><strong>Revisão:</strong> 04</div>
                    </div>
                </div>

                <div class="report-title-row">
                    <div>
                        <div class="report-title">PROGRAMAÇÃO DE PRODUÇÃO</div>
                        <div class="report-subtitle">
                            Semana <?= htmlspecialchars($semana) ?>
                            &nbsp;|&nbsp;
                            <?= formatarDataRelatorio($relatorio['periodo']['inicio']) ?>
                            a <?= formatarDataRelatorio($relatorio['periodo']['fim']) ?>
                        </div>
                    </div>
                    <?php if ($recursoId !== null && !empty($relatorio['equipamentos'])): ?>
                        <div class="report-equipment-highlight">
                            <?= htmlspecialchars($relatorio['equipamentos'][0]['nome']) ?>
                        </div>
                    <?php else: ?>
                        <div class="report-equipment-highlight">Todos os equipamentos</div>
                    <?php endif; ?>
                </div>
            </header>

            <?php if (empty($relatorio['equipamentos'])): ?>
                <div class="alert alert-info m-3 mb-0">
                    Não há programação para a semana e equipamento selecionados.
                </div>
            <?php endif; ?>

            <?php foreach ($relatorio['equipamentos'] as $equipamento): ?>
                <section class="report-equipment">
                    <div class="report-equipment-header">
                        <span><?= htmlspecialchars($equipamento['nome']) ?></span>
                        <span class="report-equipment-total">
                            Total: <?= formatarNumeroRelatorio((float) $equipamento['totais']['quantidade']) ?>
                        </span>
                    </div>

                    <?php foreach ($equipamento['dias'] as $dia): ?>
                        <div class="report-day">
                            <div class="report-day-header">
                                <span class="report-day-name"><?= nomeDiaRelatorio($dia['data']) ?></span>
                                <span><?= formatarDataRelatorio($dia['data']) ?></span>
                                <span class="report-day-total">
                                    <?= formatarNumeroRelatorio((float) $dia['totais']['quantidade']) ?> programado
                                </span>
                            </div>

                            <table class="report-table">
                                <thead>
                                    <tr>
                                        <th class="col-codigo">Código</th>
                                        <th>Descrição</th>
                                        <th class="col-demanda">Demanda</th>
                                        <th class="col-quantidade text-end">Qtd.</th>
                                        <th class="col-peso text-end">Produção estimada (t)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dia['itens'] as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars((string) $item['produto_id']) ?></td>
                                            <td>
                                                <div><?= htmlspecialchars($item['descricao']) ?></div>
                                                <?php if (!empty($item['observacao'])): ?>
                                                    <div class="report-observation">Obs.: <?= htmlspecialchars($item['observacao']) ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars((string) ($item['demanda'] ?? '')) ?></td>
                                            <td class="text-end"><?= formatarNumeroRelatorio((float) $item['quantidade']) ?></td>
                                            <td class="text-end"><?= formatarNumeroRelatorio((float) $item['peso_estimado']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end">Total do dia</td>
                                        <td class="text-end"><?= formatarNumeroRelatorio((float) $dia['totais']['quantidade']) ?></td>
                                        <td class="text-end"><?= formatarNumeroRelatorio((float) $dia['totais']['peso_estimado']) ?> t</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php endforeach; ?>
                </section>
            <?php endforeach; ?>

            <footer class="report-footer">
                <div>
                    <strong>RP 04</strong> — Programação de Produção
                </div>
                <div>
                    Revisão 04
                </div>
                <div>
                    Documento controlado — uso interno
                </div>
            </footer>
        </div>
    <?php endif; ?>
</div>

<style>
    .report-preview {
        width: 100%;
        max-width: 1120px;
        margin: 0 auto;
        color: #212529;
        font-family: Arial, Helvetica, sans-serif;
    }

    .report-header {
        border: 1px solid #343a40;
        border-bottom: 0;
    }

    .report-header-top {
        display: grid;
        grid-template-columns: 1.1fr 2fr .9fr;
        min-height: 62px;
        border-bottom: 1px solid #343a40;
    }

    .report-brand,
    .report-document-title,
    .report-document-code {
        padding: 7px 10px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .report-brand {
        border-right: 1px solid #343a40;
    }

    .report-document-title {
        text-align: center;
        font-size: .72rem;
        line-height: 1.35;
    }

    .report-document-code {
        border-left: 1px solid #343a40;
        font-size: .7rem;
        line-height: 1.45;
    }

    .report-brand-name {
        font-size: 1.35rem;
        font-weight: 800;
        letter-spacing: .04em;
        line-height: 1;
    }

    .report-brand-subtitle {
        font-size: .55rem;
        letter-spacing: .12em;
        margin-top: 3px;
    }

    .report-title-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 8px 10px;
    }

    .report-title {
        font-size: .95rem;
        font-weight: 800;
        letter-spacing: .02em;
    }

    .report-subtitle {
        font-size: .72rem;
        color: #495057;
        margin-top: 2px;
    }

    .report-equipment-highlight {
        border: 1px solid #adb5bd;
        padding: 5px 9px;
        font-size: .72rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .report-equipment {
        margin: 0 10px 12px;
        break-inside: avoid;
    }

    .report-equipment-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 5px 8px;
        background: #e9ecef;
        border: 1px solid #495057;
        border-bottom: 0;
        font-size: .78rem;
        font-weight: 700;
    }

    .report-equipment-total {
        font-weight: 600;
        font-size: .7rem;
    }

    .report-day {
        margin-bottom: 7px;
        break-inside: avoid;
    }

    .report-day-header {
        display: flex;
        align-items: center;
        gap: 7px;
        padding: 3px 7px;
        background: #f8f9fa;
        border-left: 1px solid #6c757d;
        border-right: 1px solid #6c757d;
        border-top: 1px solid #6c757d;
        font-size: .68rem;
    }

    .report-day-name {
        font-weight: 700;
    }

    .report-day-total {
        margin-left: auto;
        color: #495057;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .report-table th,
    .report-table td {
        border: 1px solid #adb5bd;
        padding: 3px 5px;
        font-size: .68rem;
        line-height: 1.2;
        vertical-align: top;
    }

    .report-table th {
        background: #f1f3f5;
        font-weight: 700;
        text-transform: uppercase;
        font-size: .61rem;
        vertical-align: middle;
    }

    .report-table .col-codigo {
        width: 11%;
    }

    .report-table .col-demanda {
        width: 13%;
    }

    .report-table .col-quantidade {
        width: 9%;
    }

    .report-table .col-peso {
        width: 13%;
    }

    .report-table tfoot td {
        background: #f8f9fa;
        font-weight: 700;
    }

    .report-observation {
        color: #6c757d;
        font-size: .6rem;
        margin-top: 2px;
    }

    .report-footer {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 8px;
        border: 1px solid #343a40;
        padding: 5px 8px;
        margin-top: 6px;
        font-size: .58rem;
        color: #343a40;
    }

    .report-footer div:nth-child(2) {
        text-align: center;
    }

    .report-footer div:nth-child(3) {
        text-align: right;
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 10mm 9mm 12mm 9mm;
        }

        body {
            background: #fff !important;
        }

        .report-toolbar,
        .app-sidebar,
        .app-header,
        .app-footer,
        nav,
        .sidebar,
        .navbar {
            display: none !important;
        }

        .report-page,
        .container-fluid {
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            max-width: none !important;
        }

        .report-preview {
            max-width: none;
            border: 0 !important;
            box-shadow: none !important;
            margin: 0 !important;
        }

        .report-header-top,
        .report-equipment-header,
        .report-day-header,
        .report-table thead {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .report-equipment,
        .report-day {
            break-inside: avoid;
        }

        .report-table thead {
            display: table-header-group;
        }

        .report-table tfoot {
            display: table-row-group;
        }

        .report-footer {
            break-inside: avoid;
        }
    }
</style>
