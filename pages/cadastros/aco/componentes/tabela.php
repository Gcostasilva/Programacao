<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table id="tabelaAcos" class="table table-hover table-striped align-middle w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo de aço</th>
                        <th>Ativo</th>
                        <th class="text-end">Ações</th>
                    </tr>
                    <tr class="filters">
                        <th><input class="form-control form-control-sm" placeholder="Filtrar"></th>
                        <th><input class="form-control form-control-sm" placeholder="Filtrar"></th>
                        <th>
                            <select class="form-select form-select-sm">
                                <option value="">Todos</option>
                                <option value="Sim">Sim</option>
                                <option value="Não">Não</option>
                            </select>
                        </th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registros as $r): ?>
                        <tr>
                            <td><?= $e($r['id']) ?></td>
                            <td><?= $e($r['tipo']) ?></td>
                            <td data-order="<?= (int) $r['ativo'] ?>">
                                <?php if ((int) $r['ativo'] === 1): ?>
                                    <span class="badge text-bg-success">Sim</span>
                                <?php else: ?>
                                    <span class="badge text-bg-secondary">Não</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end text-nowrap">
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Editar"
                                        onclick='editarAco(<?= json_encode($r, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>)'>
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <form method="post"
                                      class="d-inline"
                                      onsubmit="return confirmarExclusaoAco();">
                                    <input type="hidden" name="acao" value="excluir">
                                    <input type="hidden" name="id" value="<?= $e($r['id']) ?>">
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Excluir">
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
