<?php
$status = $_GET['status'] ?? '';
$mensagem = $_GET['msg'] ?? '';
$e = static fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
?>
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1"><i class="bi bi-layers me-2"></i>Cadastro de Tipo de Aço</h2>
            <div class="text-body-secondary">Tipos de aço disponíveis para utilização na programação.</div>
        </div>

        <button class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#modalAco"
                onclick="novoAco()">
            <i class="bi bi-plus-lg me-1"></i>Novo tipo de aço
        </button>
    </div>

    <?php if ($status === 'salvo'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            Registro salvo com sucesso.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($status === 'excluido'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            Registro excluído com sucesso.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($status === 'erro'): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $e($mensagem) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php include __DIR__ . '/componentes/tabela.php'; ?>
</div>

<?php include __DIR__ . '/componentes/formulario.php'; ?>

<script>
function novoAco() {
    document.getElementById('tituloModalAco').textContent = 'Novo tipo de aço';
    document.getElementById('aco_id').value = '';
    document.getElementById('aco_tipo').value = '';
    document.getElementById('aco_ativo').value = '1';
}

function editarAco(registro) {
    document.getElementById('tituloModalAco').textContent = 'Editar tipo de aço';
    document.getElementById('aco_id').value = registro.id;
    document.getElementById('aco_tipo').value = registro.tipo;
    document.getElementById('aco_ativo').value = registro.ativo;

    bootstrap.Modal
        .getOrCreateInstance(document.getElementById('modalAco'))
        .show();
}

function confirmarExclusaoAco() {
    return confirm(
        'Excluir este tipo de aço? Se ele já estiver sendo utilizado na programação, será necessário desativá-lo em vez de excluí-lo.'
    );
}

document.addEventListener('DOMContentLoaded', function () {
    const tabela = new DataTable('#tabelaAcos', {
        paging: false,
        scrollY: '60vh',
        scrollCollapse: true,
        order: [[1, 'asc']],
        columnDefs: [{
            targets: 3,
            orderable: false,
            searchable: false
        }],
        language: {
            emptyTable: 'Nenhum tipo de aço cadastrado.',
            zeroRecords: 'Nenhum registro encontrado.'
        }
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
