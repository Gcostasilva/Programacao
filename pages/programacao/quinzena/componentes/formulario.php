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
                <div class="col-md-2">
                    <label class="form-label">Código</label>
                    <input class="form-control" name="produto_id" id="produto_id" list="listaProdutosQuinzena" required autocomplete="off">
                    <datalist id="listaProdutosQuinzena">
                        <?php foreach ($dados['produtos'] as $produto): ?>
                            <option value="<?= htmlspecialchars($produto['codigo']) ?>"><?= htmlspecialchars($produto['descricao']) ?></option>
                        <?php endforeach; ?>
                    </datalist>
                </div>
                <div class="col-md-4">
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const mes = document.getElementById('mes_quinzena');
    const numero = document.getElementById('numero_quinzena');
    const hidden = document.getElementById('quinzena');
    const codigo = document.getElementById('produto_id');
    const descricao = document.getElementById('descricao_produto');
    const lista = <?= json_encode($dados['produtos'], JSON_UNESCAPED_UNICODE) ?>;

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
