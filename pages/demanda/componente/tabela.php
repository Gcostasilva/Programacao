<?php

$pdo = require 'c:\\xampp\\htdocs\\Programacao\\config\\banco.php';

$sql = "
WITH dados_equipamento AS (
    SELECT 
        CASE 
        when left(dm.codigo, 4) = '0301' or left(dm.codigo, 4) = '0305' then '3-Desbobinadeira 01'
        when left(dm.codigo, 4) = '0302' or left(dm.codigo, 4) = '0303' or left(dm.codigo, 4) = '0304' then '3-Desbobinadeira LCT'
        when left(dm.codigo, 2) = '04' then '4-Serralheria'
        when left(dm.codigo, 2) = '05' then '5-Perfiladeira-08'
        when left(dm.codigo, 2) = '06' then '6-Bobininha'
            ELSE '' 
        END AS equipamento, 
        dm.codigo, 
        prd.descricao, 
        prd.especial, 
        dm.armazem, 
        dm.estoque, 
        dm.pendencia AS pedido, 
        (dm.estoque - dm.pendencia) AS saldo, 
        pr.quantidade AS previsao, 
        CASE 
            WHEN pr.quantidade IS NULL OR pr.quantidade = 0 THEN 0 
            ELSE ROUND(((dm.estoque - dm.pendencia) / pr.quantidade) * 30, 0) 
        END AS dias_estoque, 
        GREATEST(IFNULL(pr.quantidade, 0) - (dm.estoque - dm.pendencia), 0) AS necessidade, 
        (IFNULL(pg.op_semanal, 0) + IFNULL(pg2.op_quinzenal, 0)) AS op_aberta 
    FROM demanda dm 
    LEFT JOIN produtos prd ON dm.codigo = prd.codigo 
    LEFT JOIN tb_previsao pr ON dm.codigo = pr.codigo AND dm.armazem = pr.armazem 
    LEFT JOIN ( 
        SELECT produto_id, SUM(qtd) AS op_semanal 
        FROM programacao 
        WHERE produto_id IS NOT NULL AND COALESCE(peca_realizada, 0) = 0 
        GROUP BY produto_id 
    ) pg ON dm.codigo = pg.produto_id 
    LEFT JOIN ( 
        SELECT produto_id, SUM(quantidade) AS op_quinzenal 
        FROM programacao_quinzenal 
        WHERE COALESCE(peca_realizada, 0) = 0 
        GROUP BY produto_id 
    ) pg2 ON dm.codigo = pg2.produto_id 
    WHERE prd.especial = 0
)
SELECT * 
FROM dados_equipamento 
WHERE equipamento <> ''; -- Excludes items that don't match any equipment group


";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$demanda = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();

$modoDemanda = $modoDemanda ?? 'pagina';
$idTabelaDemanda = $idTabelaDemanda ?? 'tb_demanda';
$modoSelecao = $modoDemanda === 'selecao';

// Detalhes usados pelo modal de OP aberta: produção realizada igual a zero
// e quantidade programada maior que zero, tanto na semanal quanto na quinzenal.
$opsAbertasPorProduto = [];
$sqlOpsAbertas = "
        SELECT
            CONVERT('Semanal' USING utf8mb4) COLLATE utf8mb4_unicode_ci AS origem,
            CONVERT(CAST(pr.produto_id AS CHAR) USING utf8mb4) COLLATE utf8mb4_unicode_ci AS produto_id,
            CONVERT(CAST(pr.id AS CHAR) USING utf8mb4) COLLATE utf8mb4_unicode_ci AS registro,
            CONVERT(COALESCE(DATE_FORMAT(pr.data, '%d/%m/%Y'), '') USING utf8mb4) COLLATE utf8mb4_unicode_ci AS periodo,
            CONVERT(COALESCE(CAST(pr.demanda AS CHAR), '') USING utf8mb4) COLLATE utf8mb4_unicode_ci AS referencia,
            CONVERT(COALESCE(mq.descricao, '') USING utf8mb4) COLLATE utf8mb4_unicode_ci AS recurso,
            COALESCE(pr.qtd, 0) AS quantidade,
            COALESCE(pr.peca_realizada, 0) AS produzida,
            CONVERT(COALESCE(pr.obs, '') USING utf8mb4) COLLATE utf8mb4_unicode_ci AS observacao
        FROM programacao pr
        LEFT JOIN maquinas mq ON mq.id = pr.maquina_id
        WHERE pr.produto_id IS NOT NULL
          AND COALESCE(pr.peca_realizada, 0) = 0
          AND COALESCE(pr.qtd, 0) > 0

        UNION ALL

        SELECT
            CONVERT('Quinzenal' USING utf8mb4) COLLATE utf8mb4_unicode_ci AS origem,
            CONVERT(CAST(pq.produto_id AS CHAR) USING utf8mb4) COLLATE utf8mb4_unicode_ci AS produto_id,
            CONVERT(COALESCE(NULLIF(pq.ordem_producao, ''), CAST(pq.id AS CHAR)) USING utf8mb4) COLLATE utf8mb4_unicode_ci AS registro,
            CONVERT(COALESCE(CAST(pq.quinzena AS CHAR), '') USING utf8mb4) COLLATE utf8mb4_unicode_ci AS periodo,
            CONVERT(COALESCE(CAST(pq.ordem_producao AS CHAR), '') USING utf8mb4) COLLATE utf8mb4_unicode_ci AS referencia,
            CONVERT('' USING utf8mb4) COLLATE utf8mb4_unicode_ci AS recurso,
            COALESCE(pq.quantidade, 0) AS quantidade,
            COALESCE(pq.peca_realizada, 0) AS produzida,
            CONVERT(COALESCE(pq.obs, '') USING utf8mb4) COLLATE utf8mb4_unicode_ci AS observacao
        FROM programacao_quinzenal pq
        WHERE COALESCE(pq.peca_realizada, 0) = 0
          AND COALESCE(pq.quantidade, 0) > 0
    ";
    $stmtOpsAbertas = $pdo->prepare($sqlOpsAbertas);
    $stmtOpsAbertas->execute();
$registrosOpsAbertas = $stmtOpsAbertas->fetchAll(PDO::FETCH_ASSOC);
$stmtOpsAbertas->closeCursor();

foreach ($registrosOpsAbertas as $op) {
    $codigoProduto = (string) $op['produto_id'];
    $opsAbertasPorProduto[$codigoProduto][] = $op;
}

$colunasNumericas = [4, 5,  6, 7, 8, 9,10];
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
                    <th>Equipamento</th>
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
                    <?php for ($i = 0; $i < 11; $i++): ?>
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
                    <tr  <?php if ($d['saldo'] < 0): ?>class="table-danger"<?php elseif ($d['previsao'] > $d['estoque']): ?>class="table-warning"<?php endif; ?>>
                        <td><?= htmlspecialchars($d['equipamento'] ?? '') ?></td>
                        <td><?= htmlspecialchars($d['codigo']) ?></td>
                        <td><?= htmlspecialchars($d['descricao'] ?? '') ?></td>
                        <td><?= htmlspecialchars($d['armazem']) ?></td>
                        <td><?= htmlspecialchars($d['estoque']) ?></td>
                        <td><?= htmlspecialchars($d['pedido']) ?></td>
                        <td><?= htmlspecialchars($d['saldo']) ?></td>
                        <td><?= htmlspecialchars($d['previsao'] ?? '') ?></td>
                        <td><?= htmlspecialchars($d['dias_estoque']) ?></td>
                        <td><?= htmlspecialchars($d['necessidade']) ?></td>
                        <td>
                            <?php if ((float) $d['op_aberta'] > 0 && !empty($opsAbertasPorProduto[(string) $d['codigo']])): ?>
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary btn-detalhes-op-aberta"
                                        title="Ver registros de OP aberta"
                                        aria-label="Ver registros de OP aberta para <?= htmlspecialchars($d['codigo'], ENT_QUOTES) ?>"
                                        data-codigo="<?= htmlspecialchars($d['codigo'], ENT_QUOTES) ?>"
                                        data-descricao="<?= htmlspecialchars($d['descricao'] ?? '', ENT_QUOTES) ?>"
                                        data-ops="<?= htmlspecialchars(json_encode($opsAbertasPorProduto[(string) $d['codigo']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES) ?>">
                                    <i class="bi bi-search"></i>
                                    <span><?= htmlspecialchars($d['op_aberta']) ?></span>
                                </button>
                            <?php else: ?>
                                <?= htmlspecialchars($d['op_aberta']) ?>
                            <?php endif; ?>
                        </td>
                        <?php if ($modoSelecao): ?>
                            <td><button type="button" class="btn btn-sm btn-primary btn-selecionar-demanda" data-codigo="<?= htmlspecialchars($d['codigo'], ENT_QUOTES) ?>"><i class="bi bi-check-lg"></i></button></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal com os registros que compõem a OP aberta do produto selecionado -->
<div class="modal fade" id="modalDetalhesOpAberta" tabindex="-1" aria-labelledby="modalDetalhesOpAbertaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="modalDetalhesOpAbertaLabel"><i class="bi bi-list-check me-2"></i>Registros de OP aberta</h5>
                    <div class="small text-body-secondary" id="modalDetalhesOpAbertaProduto"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2">
                    São listados os registros com quantidade produzida igual a zero e quantidade programada maior que zero.
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Origem</th>
                                <th>Registro / OP</th>
                                <th>Período / Data</th>
                                <th>Demanda</th>
                                <th>Recurso</th>
                                <th class="text-end">Qtd. programada</th>
                                <th class="text-end">Qtd. produzida</th>
                                <th>Observação</th>
                            </tr>
                        </thead>
                        <tbody id="modalDetalhesOpAbertaLinhas">
                            <tr><td colspan="8" class="text-center text-body-secondary">Selecione um produto para consultar os registros.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
<script>
(function () {
    const modalEl = document.getElementById('modalDetalhesOpAberta');
    if (!modalEl || modalEl.dataset.eventoLigado === '1') return;
    modalEl.dataset.eventoLigado = '1';

    // Coloca o modal de detalhes diretamente no body para funcionar também
    // quando a tabela de Demanda está dentro do modal da Programação Semanal.
    document.body.appendChild(modalEl);

    modalEl.addEventListener('show.bs.modal', function () {
        if (document.querySelectorAll('.modal.show').length > 0) {
            modalEl.style.zIndex = '1060';
        }
    });

    modalEl.addEventListener('shown.bs.modal', function () {
        const modaisAbertos = document.querySelectorAll('.modal.show').length;
        const fundos = document.querySelectorAll('.modal-backdrop');
        const ultimoFundo = fundos[fundos.length - 1];
        if (modaisAbertos > 1 && ultimoFundo) ultimoFundo.style.zIndex = '1055';
    });

    modalEl.addEventListener('hidden.bs.modal', function () {
        modalEl.style.zIndex = '';
        document.querySelectorAll('.modal-backdrop').forEach(function (fundo) {
            if (fundo.style.zIndex === '1055') fundo.style.zIndex = '';
        });
        if (document.querySelector('.modal.show')) document.body.classList.add('modal-open');
    });

    document.addEventListener('click', function (event) {
        const botao = event.target.closest('.btn-detalhes-op-aberta');
        if (!botao) return;

        let registros = [];
        try {
            registros = JSON.parse(botao.dataset.ops || '[]');
        } catch (erro) {
            console.error('Não foi possível ler os registros de OP aberta.', erro);
        }

        document.getElementById('modalDetalhesOpAbertaProduto').textContent =
            (botao.dataset.codigo || '') + ' — ' + (botao.dataset.descricao || '');

        const tbody = document.getElementById('modalDetalhesOpAbertaLinhas');
        tbody.replaceChildren();

        if (!registros.length) {
            const tr = document.createElement('tr');
            const td = document.createElement('td');
            td.colSpan = 8;
            td.className = 'text-center text-body-secondary';
            td.textContent = 'Nenhum registro em aberto encontrado para este produto.';
            tr.appendChild(td);
            tbody.appendChild(tr);
        } else {
            registros.forEach(function (registro) {
                const tr = document.createElement('tr');
                [
                    registro.origem,
                    registro.registro,
                    registro.periodo,
                    registro.referencia,
                    registro.recurso,
                    registro.quantidade,
                    registro.produzida,
                    registro.observacao
                ].forEach(function (valor, indice) {
                    const td = document.createElement('td');
                    td.textContent = valor == null ? '' : String(valor);
                    if (indice === 5 || indice === 6) td.className = 'text-end';
                    tr.appendChild(td);
                });
                tbody.appendChild(tr);
            });
        }

        if (window.bootstrap) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
    });
})();
</script>

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
        const inputs = Array.from(document.querySelectorAll('.filtro-coluna[data-coluna="' + coluna + '"]'))
            .filter(input => input.dataset.tabela === idTabela);

        // O DataTables pode duplicar o cabeçalho ao criar a área de rolagem.
        // Prioriza o campo visível no cabeçalho rolável, em vez de ler um clone oculto.
        return inputs.find(input => input.closest('.dt-scroll-head, .dataTables_scrollHead'))
            || inputs.find(input => input.getClientRects().length > 0)
            || inputs[0]
            || null;
    }

    function inicializarDemanda() {
        const tabela = document.getElementById(idTabela);
        if (!tabela) return;

        // A seleção não deve depender de a inicialização do DataTables ter sucesso.
        if (tabela.dataset.demandaSelecaoLigada !== '1') {
            document.addEventListener('click', function (event) {
                const botao = event.target.closest('.btn-selecionar-demanda');
                if (!botao) return;

                const tabelaBotao = botao.closest('table');
                if (!tabelaBotao || tabelaBotao.id !== idTabela) return;

                const campo = document.querySelector(window.demandaCampoDestino || '#codigo');
                if (!campo) {
                    console.warn('Campo de destino da demanda não encontrado:', window.demandaCampoDestino || '#codigo');
                    return;
                }

                campo.value = botao.dataset.codigo || '';
                campo.dispatchEvent(new Event('input', { bubbles: true }));
                campo.dispatchEvent(new Event('change', { bubbles: true }));
                campo.dispatchEvent(new Event('blur', { bubbles: true }));

                const modal = tabelaBotao.closest('.modal');
                if (modal && window.bootstrap) {
                    const instancia = bootstrap.Modal.getInstance(modal);
                    if (instancia) instancia.hide();
                }
            });
            tabela.dataset.demandaSelecaoLigada = '1';
        }

        if (tabela.dataset.dataTableInicializada === '1') return;
        if (typeof DataTable === 'undefined') {
            console.error('DataTables não foi carregado; a tabela de demanda não pode inicializar.');
            return;
        }

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

    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializarDemanda);
    } else {
        inicializarDemanda();
    }
})();
</script>