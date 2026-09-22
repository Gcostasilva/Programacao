<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table id="tabelaEquipamentos" class="table table-hover table-striped align-middle w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descrição</th>
                        <th>Ativo</th>
                        <th>Tipo</th>
                        <th>Capacidade kg</th>
                        <th class="text-end">Ações</th>
                    </tr>
                    <tr class="filters">
                        <th><input class="form-control form-control-sm"></th>
                        <th><input class="form-control form-control-sm"></th>
                        <th><select class="form-select form-select-sm">
                                <option value="">Todos</option>
                                <option>Sim</option>
                                <option>Não</option>
                            </select></th>
                        <th><input class="form-control form-control-sm"></th>
                        <th><input class="form-control form-control-sm"></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody><?php foreach ($registros as $r): ?><tr>
                            <td><?= $e($r['id']) ?></td>
                            <td><?= $e($r['descricao']) ?></td>
                            <td data-order="<?= $r['ativo'] ?>"><?= $r['ativo'] ? '<span class="badge text-bg-success">Sim</span>' : '<span class="badge text-bg-secondary">Não</span>' ?></td>
                            <td><?= $e($r['tipo']) ?></td>
                            <td data-order="<?= $r['capacidade'] ?>"><?= number_format((int)$r['capacidade'], 0, ',', '.') ?></td>
                            
                            <td class="text-end text-nowrap"><button class="btn btn-sm btn-outline-primary"
                                    onclick='editarEquipamento(<?= json_encode($r, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>)'>
                                    <i class="bi bi-pencil"></i></button>
                                <form method="post" class="d-inline" onsubmit="return confirmarExclusaoEquipamento();">
                                    <input type="hidden" name="acao" value="excluir">
                                    <input type="hidden" name="id" value="<?= $e($r['id']) ?>">
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr><?php endforeach; ?></tbody>
            </table>
        </div>
    </div>
</div>