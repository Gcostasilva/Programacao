<div class="card mb-4">
    <div class="card-header accordion-header " id="accordionHeader">
        <div class="card-title text-center flex-nowrap">Histórico</div>
    </div>
    <!-- /.card-header -->
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <td>Pedido</td>
                        <td>Tipo</td>
                        <td>Entrada</td>
                        <td>Previsão</td>
                        <td>Saída</td>
                        <td>Prazo</td>
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