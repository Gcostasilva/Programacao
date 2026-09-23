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
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h2 class="mb-1"><i class="bi bi-arrow-counterclockwise me-2"></i>Relatório de Estornos</h2>
            <div class="text-muted">Consulta dos estornos registrados nos pedidos.</div>
        </div>
        <button type="button" class="btn btn-outline-secondary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Imprimir</button>
    </div>

    <div class="card shadow-sm mb-3">
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

    <div class="card shadow-sm">
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
@media print {
    .main-sidebar, .app-header, .card:first-of-type, .btn, nav { display: none !important; }
    .container-fluid { padding: 0 !important; }
    .card { border: 0 !important; box-shadow: none !important; }
}
</style>
