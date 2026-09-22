<?php
$status = $_GET['status'] ?? '';
$mensagem = $_GET['msg'] ?? '';
$e = static fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
?>

<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1"><i class="bi bi-tools me-2"></i>Equipamentos</h2>
            <div class="text-body-secondary">Cadastro das máquinas/equipamentos disponíveis para a programação.</div>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEquipamento" onclick="novoEquipamento()">
            <i class="bi bi-plus-lg me-1"></i>Novo equipamento
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
                <table id="tabelaEquipamentos" class="table table-hover table-striped align-middle w-100">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Descrição</th>
                            <th>Ativo</th>
                            <th>Tipo</th>
                            <th>Capacidade</th>
                            <th class="text-end">Ações</th>
                        </tr>
                        <tr class="filters">
                            <th><input class="form-control form-control-sm" placeholder="Filtrar"></th>
                            <th><input class="form-control form-control-sm" placeholder="Filtrar descrição"></th>
                            <th><select class="form-select form-select-sm"><option value="">Todos</option><option value="Sim">Sim</option><option value="Não">Não</option></select></th>
                            <th><input class="form-control form-control-sm" placeholder="Filtrar tipo"></th>
                            <th><input class="form-control form-control-sm" placeholder="Filtrar capacidade"></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registros as $registro): ?>
                            <tr>
                                <td><?= $e($registro['id']) ?></td>
                                <td><?= $e($registro['descricao']) ?></td>
                                <td data-order="<?= (int) $registro['ativo'] ?>">
                                    <?php if ((int) $registro['ativo'] === 1): ?>
                                        <span class="badge text-bg-success">Sim</span>
                                    <?php else: ?>
                                        <span class="badge text-bg-secondary">Não</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $e($registro['tipo']) ?></td>
                                <td data-order="<?= (int) $registro['capacidade'] ?>"><?= number_format((int) $registro['capacidade'], 0, ',', '.') ?></td>
                                <td class="text-end text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-primary" title="Editar"
                                        onclick='editarEquipamento(<?= json_encode($registro, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>)'>
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="post" class="d-inline" onsubmit="return confirmarExclusaoEquipamento(this);">
                                        <input type="hidden" name="acao" value="excluir">
                                        <input type="hidden" name="id" value="<?= $e($registro['id']) ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir">
                                            <i class="bi bi-trash"></i>
                                        </button>
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

<div class="modal fade" id="modalEquipamento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloModalEquipamento">Novo equipamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="acao" value="salvar">
                    <input type="hidden" name="id" id="equipamento_id">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Descrição <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="descricao" id="equipamento_descricao" maxlength="100" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ativo</label>
                            <select class="form-select" name="ativo" id="equipamento_ativo">
                                <option value="1">Sim</option>
                                <option value="0">Não</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipo</label>
                            <input type="text" class="form-control" name="tipo" id="equipamento_tipo" maxlength="30" placeholder="Ex.: diária ou semanal">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Capacidade</label>
                            <input type="number" class="form-control" name="capacidade" id="equipamento_capacidade" min="0" step="1" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function novoEquipamento() {
    document.getElementById('tituloModalEquipamento').textContent = 'Novo equipamento';
    document.getElementById('equipamento_id').value = '';
    document.getElementById('equipamento_descricao').value = '';
    document.getElementById('equipamento_ativo').value = '1';
    document.getElementById('equipamento_tipo').value = '';
    document.getElementById('equipamento_capacidade').value = '0';
}
function editarEquipamento(registro) {
    document.getElementById('tituloModalEquipamento').textContent = 'Editar equipamento';
    document.getElementById('equipamento_id').value = registro.id;
    document.getElementById('equipamento_descricao').value = registro.descricao;
    document.getElementById('equipamento_ativo').value = registro.ativo;
    document.getElementById('equipamento_tipo').value = registro.tipo ?? '';
    document.getElementById('equipamento_capacidade').value = registro.capacidade;
    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEquipamento')).show();
}
function confirmarExclusaoEquipamento() {
    return confirm('Excluir este equipamento? A exclusão só será permitida se não houver programação vinculada a ele.');
}

document.addEventListener('DOMContentLoaded', function () {
    const tabela = new DataTable('#tabelaEquipamentos', {
        paging: false,
        scrollY: '60vh',
        scrollCollapse: true,
        order: [[1, 'asc']],
        columnDefs: [{ targets: 5, orderable: false, searchable: false }],
        language: { emptyTable: 'Nenhum registro cadastrado.', zeroRecords: 'Nenhum registro encontrado.' }
    });

    document.querySelectorAll('#tabelaEquipamentos thead tr.filters th').forEach((th, index) => {
        const input = th.querySelector('input, select');
        if (!input) return;
        input.addEventListener('click', e => e.stopPropagation());
        input.addEventListener('keyup', () => tabela.column(index).search(input.value).draw());
        input.addEventListener('change', () => tabela.column(index).search(input.value).draw());
    });
});
</script>
