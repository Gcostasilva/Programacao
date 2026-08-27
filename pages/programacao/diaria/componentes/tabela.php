<div class="card mb-4">
    <div class="card-header accordion-header " id="accordionHeader">
        <div class="card-title text-center flex-nowrap">Programação de Produção
            <input class="form-check-input" type="checkbox" id="exibir_baixados" name="exibir_baixados" role="button">
            <label class="form-check-label" for="exibir_baixados" role="button">Exibir Baixados</label>

        </div>

        <div class="row">
            <div class="mini-box-row h3" id="rowIndicadores">
            <div class="mini-box bg-primary" id="boxProgramacao">
                <div class="mini-box-valor" id="boxProgramacaoValor">--</div>
                <div class="mini-box-label h3">Programação</div>
            </div>
            <div class="mini-box bg-success" id="boxProduzido">
                <div class="mini-box-valor" id="boxProduzidoValor">--</div>
                <div class="mini-box-label">Produzido</div>
            </div>
            <div class="mini-box bg-info" id="boxSaldo">
                <div class="mini-box-valor" id="boxSaldoValor">--</div>
                <div class="mini-box-label">Saldo</div>
            </div>
            <div class="mini-box bg-danger" id="boxUtilizacao">
                <div class="mini-box-valor" id="boxUtilizacaoValor">--</div>
                <div class="mini-box-label">% Máquina</div>
            </div>
        </div>

        </div>
    </div>
    <!-- /.card-header -->
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <td>Data</td>
                        <td>Equipamento</td>
                        <td>Pedido</td>
                        <td>Vendedor</td>
                        <td>Espessura</td>
                        <td>Aço</td>
                        <td>Peso</td>
                        <td>Peso Real</td>
                        <td>Observação</td>
                    </tr>
                </thead>
                <tbody id="tabelaDados">
                    <!-- INICIO_LINHAS -->
                    <?php foreach ($tabela['tabDiaria'] as $t): ?>


                        <?php $classeLinha = ($t['falta_mp'] == 1) ? 'table-danger text-white' : ""; ?>
                        <tr class=" <?php echo $classeLinha ?>" data-id="<?= $t['id'] ?>" data-data="<?= $t['data'] ?>">

                            <td role="button">
                                <i class="bi bi-arrows-move"></i>

                                <?php

                                $hoje = new DateTime('today');
                                $dataRegistro = DateTime::createFromFormat('d/m/Y', $t['data']);

                                if ($t['peso_realizado'] > 0) {
                                    echo '<span class="badge bg-success">Baixado</span>';
                                } elseif ($dataRegistro < $hoje) {
                                    echo '<span class="badge bg-danger">Atrasado</span>';
                                } else {
                                    echo '<span class="badge bg-warning text-dark">Pendente</span>';
                                }
                                ;

                                echo " " . $t['data'];
                                ?>
                            </td>
                            <td><?= $t['recurso'] ?></td>
                            <td>
                                <?= $t['pedido'] ?>
                                <a class="btn btn-excluir" href="index.php?page=prog_diaria_excluir&id=<?= $t['id'] ?>"
                                    onclick="return confirm('Excluir registro?')">🗑️</a>
                                <a type="button" class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar"
                                    data-id="<?= $t['id'] ?>"><i class="bi bi-pencil"></i></a>
                            </td>
                            <td><?= $t['vendedor'] ?></td>
                            <td><?= number_format($t['espessura'], 2, ',', '.') ?></td>
                            <td><?= $t['aco'] ?></td>
                            <td><?= number_format($t['peso'], 0, ',', '.') ?></td>
                            <td><?= number_format($t['peso_realizado'], 0, ',', '.') ?></td>
                            <td><?= $t['obs'] ?></td>
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