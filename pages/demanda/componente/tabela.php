<?php

require_once 'c:\\xampp\\htdocs\\Programacao\\config\\banco.php';

$sql = "
SELECT
    dm.codigo AS codigo,
    prd.descricao AS descricao,
    dm.armazem AS armazem,
    dm.estoque AS estoque,
    dm.pendencia AS pedido,
    (dm.estoque - dm.pendencia) AS saldo,
    pr.quantidade AS previsao,
    CASE
        WHEN pr.quantidade IS NULL OR pr.quantidade = 0 THEN 0
        ELSE ROUND(((dm.estoque - dm.pendencia) / pr.quantidade) * 30, 0)
    END AS dias_estoque,
    GREATEST(IFNULL(pr.quantidade,0) -(dm.estoque - dm.pendencia),0) AS necessidade,
    IFNULL(pg.op_aberta,0) AS op_aberta
FROM demanda dm
LEFT JOIN produtos prd ON dm.codigo = prd.codigo
LEFT JOIN tb_previsao pr ON dm.codigo = pr.codigo AND dm.armazem = pr.armazem
LEFT JOIN (
    SELECT produto_id, SUM(qtd - peca_realizada) AS op_aberta
    FROM programacao
    WHERE peca_realizada = 0
    GROUP BY produto_id
) pg ON dm.codigo = pg.produto_id
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$demanda = $stmt->fetchAll(PDO::FETCH_ASSOC);

$modoDemanda = $modoDemanda ?? 'pagina';
$idTabelaDemanda = $idTabelaDemanda ?? 'tb_demanda';
$modoSelecao = $modoDemanda === 'selecao';
$colunasNumericas = [3, 4, 5, 6, 7, 8, 9];
?>

<div class="container-fluid">
    <div class="d-flex justify-content-end mb-2">
        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalAjudaFiltroDemanda">
            <i class="bi bi-funnel me-1"></i> Como filtrar?
        </button>
    </div>

    <div class="table-responsive">
        <table id="<?= htmlspecialchars($idTabelaDemanda) ?>" class="table table-hover table-striped align-middle" style="font-size: 0.8em; width:100%">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Descrição</th>
                    <th>Amz</th>
                    <th>Estoque</th>
                    <th>Pendência</th>
                    <th>Saldo</th>
                    <th>Previsão</th>
                    <th>Dias Estoque</th>
                    <th>Necessidade</th>
                    <th>OP Aberta</th>
                    <?php if ($modoSelecao): ?><th>Selecionar</th><?php endif; ?>
                </tr>
                <tr class="filtros-demanda">
                    <?php for ($i = 0; $i < 10; $i++): ?>
                        <th>
                            <input type="text"
                                   class="form-control form-control-sm filtro-coluna <?= in_array($i, $colunasNumericas, true) ? 'filtro-numerico' : '' ?>"
                                   data-tabela="<?= htmlspecialchars($idTabelaDemanda, ENT_QUOTES) ?>"
                                   data-coluna="<?= $i ?>"
                                   placeholder="<?= in_array($i, $colunasNumericas, true) ? 'Ex.: > 500 < 1000' : 'Filtrar...' ?>"
                                   autocomplete="off">
                        </th>
                    <?php endfor; ?>
                    <?php if ($modoSelecao): ?><th></th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($demanda as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['codigo']) ?></td>
                        <td><?= htmlspecialchars($d['descricao'] ?? '') ?></td>
                        <td><?= htmlspecialchars($d['armazem']) ?></td>
                        <td><?= htmlspecialchars($d['estoque']) ?></td>
                        <td><?= htmlspecialchars($d['pedido']) ?></td>
                        <td><?= htmlspecialchars($d['saldo']) ?></td>
                        <td><?= htmlspecialchars($d['previsao'] ?? '') ?></td>
                        <td><?= htmlspecialchars($d['dias_estoque']) ?></td>
                        <td><?= htmlspecialchars($d['necessidade']) ?></td>
                        <td><?= htmlspecialchars($d['op_aberta']) ?></td>
                        <?php if ($modoSelecao): ?>
                            <td><button type="button" class="btn btn-sm btn-primary btn-selecionar-demanda" data-codigo="<?= htmlspecialchars($d['codigo'], ENT_QUOTES) ?>"><i class="bi bi-check-lg"></i></button></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Ajuda dos filtros -->
<div class="modal fade" id="modalAjudaFiltroDemanda" tabindex="-1" aria-labelledby="modalAjudaFiltroDemandaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAjudaFiltroDemandaLabel">
                    <i class="bi bi-funnel me-2"></i>Como utilizar os filtros
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <div class="modal-body">
                <p class="mb-3">
                    Os campos logo abaixo dos nomes das colunas filtram a tabela diretamente.
                    Você pode combinar filtros de várias colunas ao mesmo tempo.
                </p>

                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-1"></i>
                    <strong>Dica:</strong> em colunas numéricas, você pode informar mais de uma condição no mesmo campo.
                </div>

                <h6 class="fw-bold mt-3">1. Filtros de texto</h6>
                <p>
                    Nas colunas de texto, basta digitar o conteúdo que deseja localizar.
                </p>
                <div class="alert alert-light border py-2">
                    <code>ABC123</code> → encontra registros que contenham <strong>ABC123</strong>.
                </div>

                <h6 class="fw-bold mt-4">2. Filtros numéricos</h6>
                <p>Nas colunas numéricas, estão disponíveis os operadores:</p>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Filtro</th>
                                <th>Significado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td><code>&gt; 500</code></td><td>maior que 500</td></tr>
                            <tr><td><code>&gt;= 500</code></td><td>maior ou igual a 500</td></tr>
                            <tr><td><code>&lt; 500</code></td><td>menor que 500</td></tr>
                            <tr><td><code>&lt;= 500</code></td><td>menor ou igual a 500</td></tr>
                            <tr><td><code>= 500</code></td><td>igual a 500</td></tr>
                            <tr><td><code>500</code></td><td>também significa igual a 500</td></tr>
                        </tbody>
                    </table>
                </div>

                <h6 class="fw-bold mt-4">3. Duas condições na mesma coluna</h6>
                <p>
                    Separe as condições por espaço. Elas são combinadas com <strong>AND</strong>.
                </p>
                <div class="alert alert-primary py-2">
                    <code>&gt; 500 &lt; 1000</code><br>
                    Resultado: valores <strong>maiores que 500 e menores que 1000</strong>.
                </div>
                <div class="alert alert-primary py-2">
                    <code>&gt;= 500 &lt;= 1000</code><br>
                    Resultado: valores entre <strong>500 e 1000, incluindo os limites</strong>.
                </div>

                <h6 class="fw-bold mt-4">4. Alternativas usando OR</h6>
                <p>
                    Use <code>OR</code> quando quiser aceitar uma condição <strong>ou</strong> outra.
                </p>
                <div class="alert alert-warning py-2">
                    <code>&lt; 100 OR &gt; 1000</code><br>
                    Resultado: valores <strong>menores que 100 ou maiores que 1000</strong>.
                </div>

                <h6 class="fw-bold mt-4">5. Filtros em várias colunas</h6>
                <p>
                    Os filtros de colunas diferentes também são combinados com <strong>AND</strong>.
                </p>
                <div class="alert alert-success py-2 mb-0">
                    <strong>Estoque:</strong> <code>&gt;= 500 &lt;= 1000</code><br>
                    <strong>Saldo:</strong> <code>&gt; 200</code><br>
                    <strong>Necessidade:</strong> <code>&lt; 5000</code><br><br>
                    Isso mostra somente registros que atendam <strong>a todas as três regras</strong>.
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<style>
#<?= htmlspecialchars($idTabelaDemanda) ?> thead tr.filtros-demanda th { padding:4px; background:var(--bs-body-bg); }
#<?= htmlspecialchars($idTabelaDemanda) ?> .filtro-coluna { min-width:70px; font-size:.9em; }
.dataTables_scrollHead .filtro-coluna { min-width:70px; font-size:.9em; }
</style>

<script>
(function () {
    const idTabela = <?= json_encode($idTabelaDemanda) ?>;
    const colunasNumericas = <?= json_encode($colunasNumericas) ?>;

    function normalizarNumero(valor) {
        let texto = String(valor ?? '').trim().replace(/\s/g, '');
        if (!texto) return NaN;
        if (texto.includes(',') && texto.includes('.')) texto = texto.replace(/\./g, '').replace(',', '.');
        else if (texto.includes(',')) texto = texto.replace(',', '.');
        return Number(texto);
    }

    function interpretarCondicaoNumerica(texto) {
        const match = texto.trim().match(/^(>=|<=|>|<|=)?\s*(-?(?:\d+(?:[.,]\d+)?|[.,]\d+))$/);
        if (!match) return null;
        const valor = normalizarNumero(match[2]);
        return Number.isFinite(valor) ? { operador: match[1] || '=', valor: valor } : null;
    }

    function compararNumero(numero, filtro) {
        switch (filtro.operador) {
            case '>': return numero > filtro.valor;
            case '>=': return numero >= filtro.valor;
            case '<': return numero < filtro.valor;
            case '<=': return numero <= filtro.valor;
            case '=': return numero === filtro.valor;
            default: return false;
        }
    }

    function interpretarFiltroNumerico(valor) {
        const texto = valor.trim();
        if (!texto) return null;

        const grupos = texto.split(/\s+OR\s+/i).map(grupo => grupo.trim()).filter(Boolean);
        if (!grupos.length) return { invalido: true };

        const resultado = [];
        for (const grupoTexto of grupos) {
            const tokens = grupoTexto.match(/(?:>=|<=|>|<|=)?\s*-?(?:\d+(?:[.,]\d+)?|[.,]\d+)/g);
            if (!tokens || tokens.join('').replace(/\s/g, '') !== grupoTexto.replace(/\s/g, '')) return { invalido: true };
            const grupo = tokens.map(interpretarCondicaoNumerica);
            if (grupo.some(condicao => !condicao)) return { invalido: true };
            resultado.push(grupo);
        }
        return { grupos: resultado, invalido: false };
    }

    function obterInput(coluna) {
        const inputs = document.querySelectorAll('.filtro-coluna[data-coluna="' + coluna + '"]');
        for (const input of inputs) if (input.dataset.tabela === idTabela) return input;
        return null;
    }

    function inicializarDemanda() {
        const tabela = document.getElementById(idTabela);
        if (!tabela || typeof DataTable === 'undefined' || tabela.dataset.dataTableInicializada === '1') return;

        const dt = new DataTable(tabela, {
            paging: false,
            scrollY: '60vh',
            scrollCollapse: true,
            orderCellsTop: true,
            language: {
                search: 'Pesquisar:',
                zeroRecords: 'Nenhum registro encontrado',
                emptyTable: 'Nenhum registro disponível',
                info: '_TOTAL_ registros'
            },
            columnDefs: [
                <?php if ($modoSelecao): ?>
                { targets: -1, orderable: false, searchable: false }
                <?php endif; ?>
            ]
        });

        tabela.dataset.dataTableInicializada = '1';

        document.addEventListener('input', function (event) {
            const input = event.target.closest('.filtro-coluna');
            if (!input || input.dataset.tabela !== idTabela) return;

            const coluna = Number(input.dataset.coluna);
            if (!Number.isInteger(coluna)) return;

            if (colunasNumericas.includes(coluna)) {
                dt.draw();
            } else {
                dt.column(coluna).search(input.value).draw();
            }
        });

        if (DataTable.ext && DataTable.ext.search) {
            DataTable.ext.search.push(function (settings, data) {
                if (settings.nTable !== tabela) return true;

                for (const coluna of colunasNumericas) {
                    const input = obterInput(coluna);
                    if (!input || !input.value.trim()) continue;

                    const filtro = interpretarFiltroNumerico(input.value);
                    if (!filtro || filtro.invalido) return false;

                    const numero = normalizarNumero(data[coluna]);
                    if (!Number.isFinite(numero)) return false;

                    const atende = filtro.grupos.some(grupo =>
                        grupo.every(condicao => compararNumero(numero, condicao))
                    );

                    if (!atende) return false;
                }

                return true;
            });
        }

        document.addEventListener('click', function (event) {
            if (event.target.closest('.filtro-coluna')) event.stopPropagation();
        }, true);

        document.addEventListener('mousedown', function (event) {
            if (event.target.closest('.filtro-coluna')) event.stopPropagation();
        }, true);

        document.addEventListener('keydown', function (event) {
            if (event.target.closest('.filtro-coluna')) event.stopPropagation();
        }, true);

        document.addEventListener('click', function (event) {
            const botao = event.target.closest('.btn-selecionar-demanda');
            if (!botao) return;

            const tabelaBotao = botao.closest('table');
            if (!tabelaBotao || tabelaBotao.id !== idTabela) return;

            const codigo = botao.dataset.codigo;
            const destino = window.demandaCampoDestino || '#codigo';
            const campo = document.querySelector(destino);
            if (!campo) return;

            campo.value = codigo;
            campo.dispatchEvent(new Event('input', { bubbles: true }));
            campo.dispatchEvent(new Event('change', { bubbles: true }));
            campo.dispatchEvent(new Event('blur', { bubbles: true }));

            const modal = tabelaBotao.closest('.modal');
            if (modal && window.bootstrap) {
                const instancia = bootstrap.Modal.getInstance(modal);
                if (instancia) instancia.hide();
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializarDemanda);
    } else {
        inicializarDemanda();
    }
})();
</script>