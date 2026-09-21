<?php

require_once __DIR__ . '/../../services/RelatorioProgramacaoService.php';
require_once __DIR__ . '/../../models/RecursoModel.php';

$inicio = $_GET['inicio'] ?? date('Y-m-d', strtotime('monday this week'));
$fim = $_GET['fim'] ?? date('Y-m-d', strtotime('friday this week'));
$recursoId = isset($_GET['recurso_id']) && $_GET['recurso_id'] !== ''
    ? filter_var($_GET['recurso_id'], FILTER_VALIDATE_INT)
    : null;

$recursoModel = new RecursoModel();
$recursos = $recursoModel->listarSemanal();

$relatorio = null;
$erro = null;

try {
    $inicioData = DateTime::createFromFormat('Y-m-d', $inicio);
    $fimData = DateTime::createFromFormat('Y-m-d', $fim);

    if (!$inicioData || !$fimData || $inicioData->format('Y-m-d') !== $inicio || $fimData->format('Y-m-d') !== $fim) {
        throw new InvalidArgumentException('Informe datas válidas.');
    }

    if ($inicio > $fim) {
        throw new InvalidArgumentException('A data inicial não pode ser maior que a data final.');
    }

    $service = new RelatorioProgramacaoService();
    $relatorio = $service->gerar($inicio, $fim, $recursoId);
} catch (Throwable $e) {
    $erro = $e->getMessage();
}

function formatarDataRelatorio(string $data): string
{
    $dt = new DateTime($data);
    return $dt->format('d/m/Y');
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

<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1">Prévia — Programação de Produção</h2>
            <div class="text-muted">Relatório planejado</div>
        </div>
        <a href="?page=relatorios" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Relatórios
        </a>
    </div>

    <form method="get" class="card shadow-sm mb-4">
        <input type="hidden" name="page" value="relatorio_programacao">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Semana</label>
                    <input type="week" class="form-control" id="semana" name="semana" value="<?php $semanaAtual = date('o-\WW');
                                                                                                echo $semanaAtual; ?>" required>
                </div>
                <input type="date" name="inicio" class="form-control" value="<?= htmlspecialchars($inicio) ?>" required>
                <input type="date" name="fim" class="form-control" value="<?= htmlspecialchars($fim) ?>" required>
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
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Gerar prévia
                    </button>
                </div>
            </div>
        </div>
    </form>

    <?php if ($erro): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($erro) ?>
        </div>
    <?php elseif ($relatorio): ?>
        <div class="report-preview bg-white border rounded shadow-sm p-4">
            <div class="report-header border-bottom pb-3 mb-3">
                <div class="row align-items-center">
                    <div class="col-md-4 fw-bold fs-4">PERFINASA</div>
                    <div class="col-md-8 text-md-end">
                        <div class="fw-semibold">SISTEMA DE GESTÃO DA QUALIDADE</div>
                        <div class="text-muted">REGISTRO DE PRODUÇÃO</div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <h3 class="mb-1">Programação de Produção</h3>
                    <div class="text-muted">
                        Período: <?= formatarDataRelatorio($relatorio['periodo']['inicio']) ?>
                        a <?= formatarDataRelatorio($relatorio['periodo']['fim']) ?>
                    </div>
                </div>
            </div>

            <?php if (empty($relatorio['equipamentos'])): ?>
                <div class="alert alert-info mb-0">
                    Não há programação para o período e equipamento selecionados.
                </div>
            <?php endif; ?>

            <?php foreach ($relatorio['equipamentos'] as $equipamento): ?>
                <section class="report-equipment mb-5">
                    <div class="report-section-title d-flex justify-content-between align-items-center border rounded-top px-3 py-2">
                        <strong><?= htmlspecialchars($equipamento['nome']) ?></strong>
                        <span class="text-muted small">
                            Total programado: <?= formatarNumeroRelatorio((float) $equipamento['totais']['peso_estimado']/1000) ?> Ton
                        </span>
                    </div>

                    <?php foreach ($equipamento['dias'] as $dia): ?>
                        <div class="mt-3">
                            <div class="report-day-title px-3 py-2 border bg-light">
                                <strong><?= nomeDiaRelatorio($dia['data']) ?></strong>
                                <span class="ms-2"><?= formatarDataRelatorio($dia['data']) ?></span>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-0 report-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 12%">Código</th>
                                            <th>Descrição</th>
                                            <th style="width: 13%">Demanda</th>
                                            <th style="width: 12%" class="text-end">Quantidade</th>
                                            <th style="width: 12%" class="text-end">Peso estimado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dia['itens'] as $item): ?>
                                            <tr>
                                                <td><?= htmlspecialchars((string) $item['produto_id']) ?></td>
                                                <td>
                                                    <?= htmlspecialchars($item['descricao']) ?>
                                                    <?php if (!empty($item['observacao'])): ?>
                                                        <div class="small text-muted">Obs.: <?= htmlspecialchars($item['observacao']) ?></div>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars((string) ($item['demanda'] ?? '')) ?></td>
                                                <td class="text-end"><?= formatarNumeroRelatorio((float) $item['quantidade']) ?></td>
                                                <td class="text-end"><?= formatarNumeroRelatorio((float) $item['peso_estimado']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="fw-semibold">
                                            <td colspan="3" class="text-end">Total do dia</td>
                                            <td class="text-end"><?= formatarNumeroRelatorio((float) $dia['totais']['quantidade']) ?></td>
                                            <td class="text-end"><?= formatarNumeroRelatorio((float) $dia['totais']['peso_estimado']) ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </section>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    .report-preview {
        max-width: 1200px;
        margin: 0 auto;
        color: #212529;
    }

    .report-section-title {
        background: #f8f9fa;
        font-size: 1.05rem;
    }

    .report-day-title {
        font-size: .95rem;
    }

    .report-table th {
        vertical-align: middle;
        font-size: .82rem;
        text-transform: uppercase;
    }

    .report-table td {
        vertical-align: top;
        font-size: .86rem;
    }

    @media print {

        .app-sidebar,
        .app-header,
        .app-footer,
        form,
        .btn {
            display: none !important;
        }

        .app-main,
        .container-fluid {
            padding: 0 !important;
            margin: 0 !important;
        }

        .report-preview {
            max-width: none;
            border: 0 !important;
            box-shadow: none !important;
            padding: 0 !important;
        }

        .report-equipment {
            break-inside: avoid;
        }

        .report-day-title,
        .report-table thead {
            break-inside: avoid;
        }
    }
</style>