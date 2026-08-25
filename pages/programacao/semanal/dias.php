<?php foreach ($dias as $numDia => $info):
    $dataDia = new DateTime();
    $dataDia->setISODate((int) substr($semana, 0, 4), (int) substr($semana, 6, 2), $numDia);
    $dataFormatada = $dataDia->format('Y-m-d');
    ?>



        <div class="card mb-4 border-primary-subtle">
            <div
                class="card-header text-primary p-0 ps-4 pt-1 <?= $dataDia->format('Y-m-d') === $hoje ? 'bg-primary bg-gradient text-light' : '' ?>">
                <h4><i class="bi bi-calendar4"></i> <?= $info['nome'] ?> - <?= $dataDia->format('d/m/Y') ?></h4>
                <h4 id="<?= $info['id'] ?>"></h4>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr class="fw-bold">
                            <td>Demanda</td>
                            <td>Produto</td>
                            <td>Descrição</td>
                            <td>Quantidade</td>
                            <td>Peso</td>
                            <td>Quantidade Realizada</td>
                            <td>Peso Realizado</td>
                            <td>Observação</td>
                        </tr>
                    </thead>
                    <tbody id="tabelaSemanal" class="tabelaSemanal" data-data="<?= $dataFormatada ?>">
                        <?php foreach ($tabela['tabSemanal'] as $t):
                            if ($t['data'] !== $dataFormatada) {
                                continue;
                            }

                            // - já realizado -> verde
                            // - data futura, ainda não realizado -> amarelo (pendente)
                            // - data passada/hoje, não realizado -> vermelho (atrasado)
                            if ($t['peca_realizada'] >= 1) {
                                $classe = 'class="table-success"';
                            } elseif ($t['data'] >= $hoje) {
                                $classe = 'class="table-warning"';
                            } else {
                                $classe = 'class="table-danger"';
                            }
                            ?>
                            <tr <?= $classe ?> data-recurso="<?= $t['maquina_id'] ?>" data-id="<?= $t['id'] ?>">
                                <td role="button">
                                    <i class="bi bi-arrows-move"></i>
                                    <a class="p-1" style="font-size: 1.2rem; color: crimson;"
                                        href="index.php?page=prog_semanal_excluir&id=<?= $t['id'] ?>"
                                        onclick="return confirm('Excluir registro?')"><i class="bi bi-trash"></i></a>
                                    <a type="button" class="p-0"
                                        style="font-size: 1.2rem; color: cornflowerblue; border: 1px black;"
                                        data-bs-toggle="modal" data-bs-target="#modalEditarSemanal" data-bs-whatever="@mdo"
                                        data-id="<?= $t['id'] ?>"><i class="bi bi-pencil"></i></a>
                                    <span
                                        class="bg-white p-md-1 rounded-pill  text-primary fw-bolder"><?= $t['demanda'] ?></span>
                                </td>
                                <td><?= $t['produto_id'] ?></td>
                                <td><?= $t['descricao'] ?></td>
                                <td><?= number_format($t['qtd'], 0, ',', '.') ?></td>
                                <td><?= number_format($t['peso'], 0, ',', '.') ?></td>
                                <td><?= number_format($t['peca_realizada'], 0, ',', '.') ?></td>
                                <td><?= number_format($t['peso_realizado'], 0, ',', '.') ?></td>
                                <td><?= $t['obs'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

<?php endforeach; ?>
