<?php
$partesQuinzena = explode('-', $quinzena);
$anoMes = $partesQuinzena[0] . '-' . $partesQuinzena[1];
$numeroQuinzena = (int)($partesQuinzena[2] ?? 1);
$anterior = $numeroQuinzena === 2 ? $anoMes . '-1' : date('Y-m', strtotime($anoMes . '-01 -1 month')) . '-2';
$proxima = $numeroQuinzena === 1 ? $anoMes . '-2' : date('Y-m', strtotime($anoMes . '-01 +1 month')) . '-1';
?>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center gap-2">
        <div class="d-flex align-items-center gap-2">
            <a class="btn btn-sm btn-outline-secondary" href="index.php?page=prog_quinzenal&quinzena=<?= urlencode($anterior) ?>" title="Quinzena anterior">
                <i class="bi bi-chevron-left"></i>
            </a>
            <h3 class="card-title mb-0">Programação de Produção — <?= htmlspecialchars($quinzena) ?></h3>
            <a class="btn btn-sm btn-outline-secondary" href="index.php?page=prog_quinzenal&quinzena=<?= urlencode($proxima) ?>" title="Próxima quinzena">
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>
        <span class="text-muted small"><?= count($tabela) ?> item(ns)</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Descrição</th>
                        <th class="text-end">A produzir</th>
                        <th class="text-end">Produzido</th>
                        <th class="text-end">Saldo</th>
                        <th>OP</th>
                        <th>Observação</th>
                        <th class="text-center" style="width:120px">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$tabela): ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">Nenhuma programação cadastrada para esta quinzena.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($tabela as $item): ?>
                        <?php
                        $quantidade = (float)($item['quantidade'] ?? 0);
                        $produzido = (float)($item['peca_realizada'] ?? 0);
                        $saldo = max(0, $quantidade - $produzido);
                        $descricao = trim((string)($item['descricao'] ?? ''));
                        ?>
                        <tr>
                            <td class="text-nowrap fw-semibold"><?= htmlspecialchars($item['produto_id']) ?></td>
                            <td><?= htmlspecialchars($descricao !== '' ? $descricao : 'Produto não localizado') ?></td>
                            <td class="text-end text-nowrap"><?= number_format($quantidade, 3, ',', '.') ?></td>
                            <td class="text-end text-nowrap">
                                <span class="badge <?= $produzido > 0 ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= number_format($produzido, 3, ',', '.') ?>
                                </span>
                            </td>
                            <td class="text-end text-nowrap fw-semibold"><?= number_format($saldo, 3, ',', '.') ?></td>
                            <td class="text-nowrap"><?= htmlspecialchars($item['ordem_producao'] ?? '') ?></td>
                            <td><?= htmlspecialchars($item['obs'] ?? '') ?></td>
                            <td class="text-center text-nowrap">
                                <button type="button" class="btn btn-sm btn-outline-primary btn-editar-quinzena" data-item='<?= htmlspecialchars(json_encode($item, JSON_UNESCAPED_UNICODE), ENT_QUOTES) ?>' title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a class="btn btn-sm btn-outline-danger" href="index.php?page=prog_quinzenal_excluir&id=<?= (int)$item['id'] ?>&quinzena=<?= urlencode($quinzena) ?>" onclick="return confirm('Excluir este item da programação quinzenal?')" title="Excluir">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarQuinzena" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" action="index.php?page=prog_quinzenal_editar">
            <div class="modal-header">
                <h5 class="modal-title">Editar Programação Quinzenal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="edit_q_id">
                <div class="row g-3">
                    <div class="col-md-4"><label class="form-label">Quinzena</label><input class="form-control" name="quinzena" id="edit_q_quinzena" readonly></div>
                    <div class="col-md-4"><label class="form-label">Código</label><input class="form-control" name="produto_id" id="edit_q_produto" required></div>
                    <div class="col-md-4"><label class="form-label">Quantidade a produzir</label><input class="form-control" type="number" name="quantidade" id="edit_q_quantidade" min="0.001" step="0.001" required></div>
                    <div class="col-md-4"><label class="form-label">Produzido</label><input class="form-control" type="number" name="peca_realizada" id="edit_q_produzido" min="0" step="0.001" required></div>
                    <div class="col-md-4"><label class="form-label">Ordem de Produção</label><input class="form-control" name="ordem_producao" id="edit_q_op"></div>
                    <div class="col-md-4"><label class="form-label">Observação</label><input class="form-control" name="obs" id="edit_q_obs"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Salvar alterações</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-editar-quinzena').forEach(function (botao) {
        botao.addEventListener('click', function () {
            const item = JSON.parse(this.dataset.item);
            document.getElementById('edit_q_id').value = item.id;
            document.getElementById('edit_q_quinzena').value = item.quinzena;
            document.getElementById('edit_q_produto').value = item.produto_id;
            document.getElementById('edit_q_quantidade').value = item.quantidade;
            document.getElementById('edit_q_produzido').value = item.peca_realizada;
            document.getElementById('edit_q_op').value = item.ordem_producao || '';
            document.getElementById('edit_q_obs').value = item.obs || '';
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditarQuinzena')).show();
        });
    });
});
</script>
