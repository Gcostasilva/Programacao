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
$vendedores = $model->listarVendedores();
$total = count($registros);
?>
<div class="container-fluid py-3">
    <div class="er-report-header">
        <div><h1>RELATÓRIO DE ESTORNOS</h1><small>Registro de estornos dos pedidos</small></div>
        <div class="er-report-meta"><strong>PERFINASA</strong><br>Senador Canedo - GO<br>Emitido em <?= date('d/m/Y H:i') ?></div>
    </div>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4 er-toolbar">
        <div>
            <h2 class="mb-1"><i class="bi bi-arrow-counterclockwise me-2"></i>Relatório de Estornos</h2>
            <div class="text-muted">Consulta dos estornos registrados nos pedidos.</div>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-success" href="?page=relatorio_estornos_excel&amp;pedido=<?= urlencode($filtros['pedido']) ?>&amp;vendedor_id=<?= (int)$filtros['vendedor_id'] ?>&amp;tipo=<?= urlencode($filtros['tipo']) ?>&amp;data_inicio=<?= urlencode($filtros['data_inicio']) ?>&amp;data_fim=<?= urlencode($filtros['data_fim']) ?>"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
            <button type="button" class="btn btn-outline-secondary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Imprimir</button>
        </div>
    </div>

    <div class="card shadow-sm mb-3 er-toolbar">
        <div class="card-body">
            <form method="get" class="row g-3 align-items-end">
                <input type="hidden" name="page" value="relatorio_estornos">
                <div class="col-md-2">
                    <label class="form-label">Pedido</label>
                    <input type="text" class="form-control" name="pedido" maxlength="6" value="<?= htmlspecialchars($filtros['pedido']) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Vendedor</label>
                    <select class="form-select" name="vendedor_id">
                        <option value="0">Todos</option>
                        <?php foreach ($vendedores as $v): ?>
                            <option value="<?= (int)$v['id'] ?>" <?= $filtros['vendedor_id'] === (int)$v['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($v['nome']) ?><?= (int)$v['ativo'] !== 1 ? ' (inativo)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipo</label>
                    <select class="form-select" name="tipo">
                        <option value="">Todos</option>
                        <option value="parcial" <?= $filtros['tipo'] === 'parcial' ? 'selected' : '' ?>>Parcial</option>
                        <option value="total" <?= $filtros['tipo'] === 'total' ? 'selected' : '' ?>>Total</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">De</label>
                    <input type="date" class="form-control" name="data_inicio" value="<?= htmlspecialchars($filtros['data_inicio']) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Até</label>
                    <input type="date" class="form-control" name="data_fim" value="<?= htmlspecialchars($filtros['data_fim']) ?>">
                </div>
                <div class="col-md-1 d-grid">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm er-document">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Estornos encontrados</strong>
            <span class="badge text-bg-secondary"><?= $total ?></span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Pedido</th>
                            <th>Atendimento</th>
                            <th>Vendedor</th>
                            <th>Tipo</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!$registros): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Nenhum estorno encontrado.</td></tr>
                    <?php else: ?>
                        <?php foreach ($registros as $r): ?>
                            <tr>
                                <td><?= $r['data_estorno'] ? date('d/m/Y H:i', strtotime($r['data_estorno'])) : '-' ?></td>
                                <td><strong><?= htmlspecialchars($r['pedido']) ?></strong></td>
                                <td><?= htmlspecialchars($r['atendimento']) ?></td>
                                <td><?= htmlspecialchars($r['vendedor']) ?></td>
                                <td>
                                    <span class="badge <?= $r['total_parcial'] === 'total' ? 'text-bg-danger' : 'text-bg-warning' ?>">
                                        <?= $r['total_parcial'] === 'total' ? 'Total' : 'Parcial' ?>
                                    </span>
                                </td>
                                <td><?= nl2br(htmlspecialchars($r['motivo'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<style>
.er-document { max-width: 1400px; margin: 0 auto; }
.er-report-header { display: none; }

@media print {
    @page { size: A4 landscape; margin: 10mm; }
    html, body { background: #fff !important; }
    body { color: #111 !important; }
    .main-sidebar, .app-sidebar, .app-header, .app-footer, nav,
    .er-toolbar, .btn, .sidebar, header:not(.er-report-header) { display: none !important; }
    .app-main, .app-content, .container-fluid { margin: 0 !important; padding: 0 !important; width: 100% !important; max-width: none !important; }
    .er-document { max-width: none !important; margin: 0 !important; border: 0 !important; box-shadow: none !important; }
    .er-report-header { display: flex !important; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #111; padding-bottom: 10px; margin-bottom: 14px; }
    .er-report-header h1 { margin: 0; font-size: 20px; }
    .er-report-header small { display: block; margin-top: 4px; color: #555; }
    .er-report-meta { text-align: right; font-size: 11px; }
    .table-responsive { overflow: visible !important; }
    table { width: 100% !important; border-collapse: collapse !important; font-size: 10px !important; }
    th, td { border: 1px solid #999 !important; padding: 5px 6px !important; }
    thead { display: table-header-group; }
    tr { break-inside: avoid; }
    .badge { border: 0 !important; color: #111 !important; background: transparent !important; padding: 0 !important; font-weight: 700; }
    .card-header { border-bottom: 1px solid #111 !important; background: #eee !important; }
}
</style>

