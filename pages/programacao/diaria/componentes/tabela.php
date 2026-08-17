<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Programação de Produção</h3>
    </div>
    <!-- /.card-header -->
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-sm">
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
                    <?php foreach ($tabela['tabDiaria'] as $t): ?>
                        <tr>
                            <td>
                                <?php
                               
                                $hoje = new DateTime();
                                $hoje = $hoje->format('d/m/Y');
                                
                                if ($t['peso_realizado'] > 0) {
                                    echo '<span class="badge bg-success">Baixado</span>';
                                } elseif ($t['data'] < $hoje ) {
                                    echo '<span class="badge bg-danger">Atrasado</span>';
                                } elseif ($t['data'] > $hoje ) {
                                    echo '<span class="badge bg-warning text-dark">Pendente</span>';
                                };
                                
                                echo $hoje . " " . $t['data'];
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
                </tbody>
            </table>
        </div>
    </div>

    <!-- /.card-body -->
</div>
<!-- /.card -->