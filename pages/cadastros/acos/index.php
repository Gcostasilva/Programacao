<?php
$status = $_GET['status'] ?? '';
$mensagem = $_GET['msg'] ?? '';
$e = static fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
?>

<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1"><i class="bi bi-box-seam me-2"></i>Aço</h2>
            <div class="text-body-secondary">Cadastro dos produtos de aço utilizados pelo sistema.</div>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAco" onclick="novoAco()">
            <i class="bi bi-plus-lg me-1"></i>Novo aço
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
                <table id="tabelaAcos" class="table table-hover table-striped align-middle w-100">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Descrição</th>
                            <th>Grupo</th>
                            <th>Especial</th>
                            <th>Peso líquido</th>
                            <th>Espessura</th>
                            <th>Ativo</th>
                            <th class="text-end">Ações</th>
                        </tr>
                        <tr class="filters">
                            <th><input class="form-control form-control-sm" placeholder="Filtrar código"></th>
                            <th><input class="form-control form-control-sm" placeholder="Filtrar descrição"></th>
                            <th><input class="form-control form-control-sm" placeholder="Filtrar grupo"></th>
                            <th><select class="form-select form-select-sm"><option value="">Todos</option><option value="Sim">Sim</option><option value="Não">Não</option></select></th>
                            <th><input class="form-control form-control-sm" placeholder="Filtrar"></th>
                            <th><input class="form-control form-control-sm" placeholder="Filtrar"></th>
                            <th><select class="form-select form-select-sm"><option value="">Todos</option><option value="Sim">Sim</option><option value="Não">Não</option></select></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registros as $registro): ?>
                            <tr>
                                <td><?= $e($registro['codigo']) ?></td>
                                <td><?= $e($registro['descricao']) ?></td>
                                <td><?= $e($registro['grupo']) ?></td>
                                <td data-order="<?= (int) $registro['especial'] ?>"><?= (int) $registro['especial'] === 1 ? 'Sim' : 'Não' ?></td>
                                <td data-order="<?= $e($registro['peso_liquido']) ?>"><?= number_format((float) $registro['peso_liquido'], 6, ',', '.') ?></td>
                                <td data-order="<?= $e($registro['espessura']) ?>"><?= number_format((float) $registro['espessura'], 2, ',', '.') ?></td>
                                <td data-order="<?= (int) $registro['ativo'] ?>">
                                    <?php if ((int) $registro['ativo'] === 1): ?>
                                        <span class="badge text-bg-success">Sim</span>
                                    <?php else: ?>
                                        <span class="badge text-bg-secondary">Não</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-primary" title="Editar"
                                        onclick='editarAco(<?= json_encode($registro, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>)'>
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="post" class="d-inline" onsubmit="return confirmarExclusaoAco(this);">
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

<div class="modal fade" id="modalAco" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloModalAco">Novo aço</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="acao" value="salvar">
                    <input type="hidden" name="id" id="aco_id">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Código <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="codigo" id="aco_codigo" maxlength="30" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Descrição <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="descricao" id="aco_descricao" maxlength="150" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Grupo</label>
                            <input type="text" class="form-control" name="grupo" id="aco_grupo" maxlength="50">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Especial</label>
                            <select class="form-select" name="especial" id="aco_especial">
                                <option value="0">Não</option>
                                <option value="1">Sim</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ativo</label>
                            <select class="form-select" name="ativo" id="aco_ativo">
                                <option value="1">Sim</option>
                                <option value="0">Não</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Peso líquido</label>
                            <input type="number" class="form-control" name="peso_liquido" id="aco_peso" min="0" step="0.000001" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Espessura</label>
                            <input type="number" class="form-control" name="espessura" id="aco_espessura" min="0" step="0.01" value="0">
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
function novoAco() {
    document.getElementById('tituloModalAco').textContent = 'Novo aço';
    document.getElementById('aco_id').value = '';
    document.getElementById('aco_codigo').value = '';
    document.getElementById('aco_descricao').value = '';
    document.getElementById('aco_grupo').value = '';
    document.getElementById('aco_especial').value = '0';
    document.getElementById('aco_peso').value = '0';
    document.getElementById('aco_espessura').value = '0';
    document.getElementById('aco_ativo').value = '1';
}
function editarAco(registro) {
    document.getElementById('tituloModalAco').textContent = 'Editar aço';
    document.getElementById('aco_id').value = registro.id;
    document.getElementById('aco_codigo').value = registro.codigo;
    document.getElementById('aco_descricao').value = registro.descricao;
    document.getElementById('aco_grupo').value = registro.grupo ?? '';
    document.getElementById('aco_especial').value = registro.especial;
    document.getElementById('aco_peso').value = registro.peso_liquido;
    document.getElementById('aco_espessura').value = registro.espessura;
    document.getElementById('aco_ativo').value = registro.ativo;
    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalAco')).show();
}
function confirmarExclusaoAco() {
    return confirm('Excluir este aço/produto? A exclusão só será permitida se não houver nenhum registro já cadastrado utilizando este código.');
}

document.addEventListener('DOMContentLoaded', function () {
    const tabela = new DataTable('#tabelaAcos', {
        paging: false,
        scrollY: '60vh',
        scrollCollapse: true,
        order: [[0, 'asc']],
        columnDefs: [{ targets: 7, orderable: false, searchable: false }],
        language: { emptyTable: 'Nenhum registro cadastrado.', zeroRecords: 'Nenhum registro encontrado.' }
    });

    document.querySelectorAll('#tabelaAcos thead tr.filters th').forEach((th, index) => {
        const input = th.querySelector('input, select');
        if (!input) return;
        input.addEventListener('click', e => e.stopPropagation());
        input.addEventListener('keyup', () => tabela.column(index).search(input.value).draw());
        input.addEventListener('change', () => tabela.column(index).search(input.value).draw());
    });
});
</script>
