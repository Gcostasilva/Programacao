<?php
$status = $_GET['status'] ?? '';
$mensagem = $_GET['msg'] ?? '';
$e = static fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
?>
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1"><i class="bi bi-arrow-counterclockwise me-2"></i>Cadastro de Motivos de Estorno</h2>
            <div class="text-body-secondary">Motivos disponíveis para registro de estornos de pedidos.</div>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalMotivoEstorno" onclick="novoMotivoEstorno()">
            <i class="bi bi-plus-lg me-1"></i>Novo motivo
        </button>
    </div>

    <?php if ($status === 'salvo'): ?>
        <div class="alert alert-success alert-dismissible fade show">Registro salvo com sucesso.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php elseif ($status === 'excluido'): ?>
        <div class="alert alert-success alert-dismissible fade show">Registro excluído com sucesso.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php elseif ($status === 'erro'): ?>
        <div class="alert alert-danger alert-dismissible fade show"><?= $e($mensagem) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabelaMotivosEstorno" class="table table-hover table-striped align-middle w-100">
                    <thead>
                        <tr><th>ID</th><th>Motivo</th><th>Ativo</th><th class="text-end">Ações</th></tr>
                        <tr class="filters">
                            <th><input class="form-control form-control-sm" placeholder="Filtrar"></th>
                            <th><input class="form-control form-control-sm" placeholder="Filtrar"></th>
                            <th><select class="form-select form-select-sm"><option value="">Todos</option><option value="Sim">Sim</option><option value="Não">Não</option></select></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($registros as $r): ?>
                        <tr>
                            <td><?= $e($r['id']) ?></td>
                            <td><?= $e($r['motivo']) ?></td>
                            <td data-order="<?= (int) $r['ativo'] ?>">
                                <?php if ((int) $r['ativo'] === 1): ?><span class="badge text-bg-success">Sim</span><?php else: ?><span class="badge text-bg-secondary">Não</span><?php endif; ?>
                            </td>
                            <td class="text-end text-nowrap">
                                <button type="button" class="btn btn-sm btn-outline-primary" title="Editar" onclick='editarMotivoEstorno(<?= json_encode($r, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>)'><i class="bi bi-pencil"></i></button>
                                <form method="post" class="d-inline" onsubmit="return confirmarExclusaoMotivoEstorno();">
                                    <input type="hidden" name="acao" value="excluir"><input type="hidden" name="id" value="<?= $e($r['id']) ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalMotivoEstorno" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloModalMotivoEstorno">Novo motivo de estorno</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="acao" value="salvar"><input type="hidden" name="id" id="motivo_estorno_id">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Motivo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="motivo" id="motivo_estorno_motivo" maxlength="100" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ativo</label>
                            <select class="form-select" name="ativo" id="motivo_estorno_ativo"><option value="1">Sim</option><option value="0">Não</option></select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Salvar</button></div>
            </form>
        </div>
    </div>
</div>

<script>
function novoMotivoEstorno() {
    document.getElementById('tituloModalMotivoEstorno').textContent = 'Novo motivo de estorno';
    document.getElementById('motivo_estorno_id').value = '';
    document.getElementById('motivo_estorno_motivo').value = '';
    document.getElementById('motivo_estorno_ativo').value = '1';
}
function editarMotivoEstorno(registro) {
    document.getElementById('tituloModalMotivoEstorno').textContent = 'Editar motivo de estorno';
    document.getElementById('motivo_estorno_id').value = registro.id;
    document.getElementById('motivo_estorno_motivo').value = registro.motivo;
    document.getElementById('motivo_estorno_ativo').value = registro.ativo;
    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalMotivoEstorno')).show();
}
function confirmarExclusaoMotivoEstorno() {
    return confirm('Excluir este motivo de estorno? Se ele já estiver sendo utilizado, será necessário desativá-lo em vez de excluí-lo.');
}
document.addEventListener('DOMContentLoaded', function () {
    const tabela = new DataTable('#tabelaMotivosEstorno', {
        paging: false, scrollY: '60vh', scrollCollapse: true, order: [[1, 'asc']],
        columnDefs: [{targets: 3, orderable: false, searchable: false}],
        language: {emptyTable: 'Nenhum motivo de estorno cadastrado.', zeroRecords: 'Nenhum registro encontrado.'}
    });
    document.querySelectorAll('#tabelaMotivosEstorno thead tr.filters th').forEach((th, index) => {
        const input = th.querySelector('input, select'); if (!input) return;
        input.addEventListener('click', e => e.stopPropagation());
        input.addEventListener('keyup', () => tabela.column(index).search(input.value).draw());
        input.addEventListener('change', () => tabela.column(index).search(input.value).draw());
    });
});
</script>
