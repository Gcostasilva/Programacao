<?php
$status = $_GET['status'] ?? '';
$mensagem = $_GET['msg'] ?? '';
$e = static fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
?>
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1"><i class="bi bi-people me-2"></i>Vendedores</h2>
            <div class="text-body-secondary">Cadastro e manutenção dos vendedores.</div>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalVendedor" onclick="novoVendedor()"><i class="bi bi-plus-lg me-1"></i>Novo vendedor</button>
    </div>
    <?php if ($status === 'salvo'): ?><div class="alert alert-success alert-dismissible fade show">Registro salvo com sucesso.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
    <?php if ($status === 'excluido'): ?><div class="alert alert-success alert-dismissible fade show">Registro excluído com sucesso.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
    <?php if ($status === 'erro'): ?><div class="alert alert-danger alert-dismissible fade show"><?= $e($mensagem) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
    <?php include __DIR__ . '/componentes/tabela.php'; ?>
</div>
<?php include __DIR__ . '/componentes/formulario.php'; ?>
<script>
    function novoVendedor() {
        document.getElementById('tituloModalVendedor').textContent = 'Novo vendedor';
        document.getElementById('vendedor_id').value = '';
        document.getElementById('vendedor_nome').value = '';
        document.getElementById('vendedor_ativo').value = '1';
    }

    function editarVendedor(r) {
        document.getElementById('tituloModalVendedor').textContent = 'Editar vendedor';
        document.getElementById('vendedor_id').value = r.id;
        document.getElementById('vendedor_nome').value = r.nome;
        document.getElementById('vendedor_ativo').value = r.ativo;
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalVendedor')).show();
    }

    function confirmarExclusaoVendedor() {
        return confirm('Excluir este vendedor? A exclusão só será permitida se não houver registros vinculados.');
    }

    document.addEventListener('DOMContentLoaded', () => {
        const tabela = new DataTable('#tabelaVendedores', {
            paging: false,
            scrollY: '60vh',
            scrollCollapse: true,
            order: [
                [1, 'asc']
            ],
            columnDefs: [{
                targets: 3,
                orderable: false,
                searchable: false
            }],
            language: {
                emptyTable: 'Nenhum registro cadastrado.',
                zeroRecords: 'Nenhum registro encontrado.'
            }
        });
        document.querySelectorAll('#tabelaVendedores thead tr.filters th').forEach((th, i) => {
            const el = th.querySelector('input,select');
            if (!el) return;
            el.addEventListener('click', e => e.stopPropagation());
            el.addEventListener('keyup', () => tabela.column(i).search(el.value).draw());
            el.addEventListener('change', () => tabela.column(i).search(el.value).draw());
        });
    });
</script>