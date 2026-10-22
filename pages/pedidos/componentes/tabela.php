<div class="card mb-4 pedidos-historico-card">
    <div class="card-header accordion-header " id="accordionHeader">
        <div class="card-title d-flex align-items-center gap-2"><i class="bi bi-clock-history"></i><span>Histórico de pedidos</span></div>
    </div>
    <!-- /.card-header -->
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead>
                    <tr>
                        <th scope="col">Pedido</th>
                        <th scope="col">Tipo</th>
                        <th scope="col">Entrada</th>
                        <th scope="col">Previsão</th>
                        <th scope="col">Saída</th>
                        <th scope="col">Prazo</th>
                    </tr>
                </thead>
                <tbody id="tabelaDados">
                    <!-- INICIO_LINHAS -->
                    <?php foreach ($tabela['tabPedidos'] as $p): ?>
                        <tr>
                            <td><?=  $p['pedido'] ?></td>
                            <td><?=  $p['tipo_nome'] ?></td>
                            <td><?= !empty($p['entrada']) ? date('d-m-Y H:i', strtotime($p['entrada'])) . ($p['user_entrada'] ? '-' . strtoupper($p['user_entrada']) : '') : '-' ?></td>
                            <td><?=  (!empty($p['previsao']) && $p['previsao'] !== '0000-00-00 00:00:00') ? date('d-m-Y', strtotime($p['previsao'])) :'' ?></td>
                            <td><?= (!empty($p['saida']) && $p['saida'] !== '0000-00-00 00:00:00') ? date('d-m-Y H:i', strtotime($p['saida'])) . ($p['user_saida'] ? '-' . strtoupper($p['user_saida']) : '') : '' ?></td>
                            <td><?=  $p['prazo']  ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <!-- FIM_LINHAS -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- /.card-body -->
</div>
<!-- /.card -->

<style>
.pedidos-historico-card {
    border: 0;
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 .35rem 1.25rem rgba(30, 41, 59, .08);
}
.pedidos-historico-card > .card-header {
    padding: 1rem 1.25rem;
    background: linear-gradient(120deg, rgba(var(--bs-primary-rgb), .11), rgba(var(--bs-primary-rgb), .025));
    border-bottom: 1px solid rgba(var(--bs-primary-rgb), .12);
}
.pedidos-historico-card .card-title { margin: 0; font-size: 1.05rem; font-weight: 700; }
.pedidos-historico-card .card-title i { color: var(--bs-primary); font-size: 1.2rem; }
.pedidos-historico-card .table-responsive { max-height: 65vh; overflow: auto; }
.pedidos-historico-card .table { --bs-table-bg: transparent; font-size: .88rem; margin: 0; }
.pedidos-historico-card .table thead th {
    position: sticky; top: 0; z-index: 1; padding: .8rem .75rem;
    white-space: nowrap; background: var(--bs-tertiary-bg); color: var(--bs-secondary-color);
    border-bottom: 1px solid var(--bs-border-color); font-size: .74rem;
    font-weight: 750; text-transform: uppercase; letter-spacing: .045em;
}
.pedidos-historico-card .table tbody td { padding: .7rem .75rem; border-color: var(--bs-border-color-translucent); }
.pedidos-historico-card .table tbody tr:hover td { --bs-table-bg: rgba(var(--bs-primary-rgb), .035); }
</style>
