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
                        <td>Usu Saída</td>
                        <td>Prazo</td>
                    </tr>
                </thead>
                <tbody id="tabelaDados">
                    <!-- INICIO_LINHAS -->
                    <?php foreach ($tabela['tabPedidos'] as $p): ?>
                        <tr>
                            <td><?=  $p['pedido'] ?></td>
                            <td><?=  $p['tipo'] ?></td>
                            <td><?=  $p['entrada'] ?></td>
                            <td><?=  $p['user_entrada'] ?></td>
                            <td><?=  $p['previsao'] ?></td>
                            <td><?=  $p['saida'] ?></td>
                            <td><?=  $p['user_saida'] ?></td>
                            <td><?=  $p['previsao'] ?></td>
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