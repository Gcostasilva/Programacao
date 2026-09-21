<?php
$quinzenaAtual = $dados['quinzena'] ?? (date('Y-m') . '-' . (date('d') <= 15 ? '1' : '2'));
?>

<div class="card card-primary card-outline" id="cardFormQuinzena">
    <div class="card-header" style="cursor:pointer;">
        <h3 class="card-title">Nova Programação Quinzenal</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" id="btnToggleFormQuinzena" title="Expandir/Retrair"><i class="bi bi-chevron-down" id="iconeToggleFormQuinzena"></i></button>
        </div>
    </div>
    <div class="card-body" id="bodyFormQuinzena">
        <form action="index.php?page=prog_quinzenal_salvar" method="POST" id="formQuinzena">
            <div class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Mês</label>
                    <input type="month" class="form-control" id="mes_quinzena" value="<?= htmlspecialchars(substr($quinzenaAtual, 0, 7)) ?>" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Quinzena</label>
                    <select class="form-select" id="numero_quinzena" required>
                        <option value="1" <?= substr($quinzenaAtual, -1) === '1' ? 'selected' : '' ?>>1ª quinzena</option>
                        <option value="2" <?= substr($quinzenaAtual, -1) === '2' ? 'selected' : '' ?>>2ª quinzena</option>
                    </select>
                    <input type="hidden" name="quinzena" id="quinzena" value="<?= htmlspecialchars($quinzenaAtual) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Código</label>
                    <div class="input-group">
                        <input class="form-control" name="produto_id" id="produto_id" required autocomplete="off">
                        <button class="btn btn-primary" type="button" id="btn_buscaCodigoQuinzena" title="Selecionar código"><i class="bi bi-search"></i></button>
                        <button class="btn btn-primary" type="button" id="btn_buscaDemandaQuinzena" title="Selecionar demanda"><i class="bi bi-boxes"></i></button>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Descrição</label>
                    <input class="form-control" id="descricao_produto" readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Quantidade a produzir</label>
                    <input class="form-control" type="number" name="quantidade" min="0.001" step="0.001" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Produzido</label>
                    <input class="form-control" type="number" name="peca_realizada" min="0" step="0.001" value="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Ordem de Produção</label>
                    <input class="form-control" name="ordem_producao">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Observação</label>
                    <input class="form-control" name="obs">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-primary flex-fill" type="submit"><i class="bi bi-save"></i> Salvar</button>
                    <button class="btn btn-outline-secondary" type="reset"><i class="bi bi-x-circle"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalCodigosQuinzena" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-search me-2"></i>Selecionar produto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="bi bi-filter"></i></span>
                    <input type="text" class="form-control" id="filtroCodigosQuinzena" placeholder="Filtrar por código ou descrição..." autocomplete="off">
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle" id="tabelaCodigosQuinzena">
                        <thead>
                            <tr><th>Código</th><th>Descrição</th><th>Peso líquido</th><th></th></tr>
                        </thead>
                        <tbody>
                            <?php foreach (($dados['produtos'] ?? []) as $produto): ?>
                                <tr data-busca="<?= htmlspecialchars(strtolower(($produto['codigo'] ?? '') . ' ' . ($produto['descricao'] ?? '')), ENT_QUOTES) ?>">
                                    <td><?= htmlspecialchars($produto['codigo'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($produto['descricao'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($produto['peso_liquido'] ?? '') ?></td>
                                    <td class="text-end"><button type="button" class="btn btn-sm btn-primary btn-selecionar-codigo-quinzena" data-codigo="<?= htmlspecialchars($produto['codigo'] ?? '', ENT_QUOTES) ?>" data-descricao="<?= htmlspecialchars($produto['descricao'] ?? '', ENT_QUOTES) ?>"><i class="bi bi-check-lg"></i></button></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/modal_demanda_selecao.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const mes = document.getElementById('mes_quinzena');
    const numero = document.getElementById('numero_quinzena');
    const hidden = document.getElementById('quinzena');
    const codigo = document.getElementById('produto_id');
    const descricao = document.getElementById('descricao_produto');
    const lista = <?= json_encode($dados['produtos'] ?? [], JSON_UNESCAPED_UNICODE) ?>;

    function atualizarQuinzena() {
        if (mes.value && numero.value) hidden.value = mes.value + '-' + numero.value;
    }
    function atualizarDescricao() {
        const produto = lista.find(p => String(p.codigo) === String(codigo.value).trim());
        descricao.value = produto ? (produto.descricao || '') : '';
    }

    mes?.addEventListener('change', atualizarQuinzena);
    numero?.addEventListener('change', atualizarQuinzena);
    codigo?.addEventListener('input', atualizarDescricao);
    atualizarQuinzena();
    atualizarDescricao();

    const modalEl = document.getElementById('modalCodigosQuinzena');
    const modal = modalEl ? bootstrap.Modal.getOrCreateInstance(modalEl) : null;
    document.getElementById('btn_buscaCodigoQuinzena')?.addEventListener('click', () => modal?.show());

    document.getElementById('filtroCodigosQuinzena')?.addEventListener('input', function () {
        const termo = this.value.trim().toLowerCase();
        document.querySelectorAll('#tabelaCodigosQuinzena tbody tr').forEach(tr => {
            tr.style.display = (tr.dataset.busca || '').includes(termo) ? '' : 'none';
        });
    });

    document.querySelectorAll('.btn-selecionar-codigo-quinzena').forEach(btn => {
        btn.addEventListener('click', function () {
            codigo.value = this.dataset.codigo || '';
            descricao.value = this.dataset.descricao || '';
            modal?.hide();
            codigo.focus();
        });
    });

    const body = document.getElementById('bodyFormQuinzena');
    const botao = document.getElementById('btnToggleFormQuinzena');
    const icone = document.getElementById('iconeToggleFormQuinzena');
    function alternar() {
        body.style.display = body.style.display === 'none' ? '' : 'none';
        icone.className = body.style.display === 'none' ? 'bi bi-chevron-right' : 'bi bi-chevron-down';
    }
    botao?.addEventListener('click', alternar);
    document.querySelector('#cardFormQuinzena .card-header')?.addEventListener('click', e => {
        if (!e.target.closest('button')) alternar();
    });
});
</script>
