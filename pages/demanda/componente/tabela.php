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
                                   placeholder="<?= in_array($i, $colunasNumericas, true) ? 'Ex.: > 500' : 'Filtrar...' ?>"
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

<style>
#<?= htmlspecialchars($idTabelaDemanda) ?> thead tr.filtros-demanda th { padding:4px; background:var(--bs-body-bg); }
#<?= htmlspecialchars($idTabelaDemanda) ?> .filtro-coluna { min-width:70px; font-size:.9em; }
.dataTables_scrollHead .filtro-coluna { min-width:70px; font-size:.9em; }
</style>

<script>
(function () {
    const idTabela = <?= json_encode($idTabelaDemanda) ?>;
    const colunasNumericas = <?= json_encode($colunasNumericas) ?>;

    function interpretarFiltroNumerico(valor) {
        const texto = valor.trim().replace(',', '.');
        if (!texto) return null;

        const correspondencia = texto.match(/^(>=|<=|>|<|=)?\s*(-?\d+(?:\.\d+)?)$/);
        if (!correspondencia) return { invalido: true };

        return {
            operador: correspondencia[1] || '=',
            valor: Number(correspondencia[2]),
            invalido: false
        };
    }

    function compararNumero(numero, filtro) {
        switch (filtro.operador) {
            case '>':  return numero > filtro.valor;
            case '>=': return numero >= filtro.valor;
            case '<':  return numero < filtro.valor;
            case '<=': return numero <= filtro.valor;
            case '=':  return numero === filtro.valor;
            default:   return true;
        }
    }

    function obterTextoCelula(celula) {
        return celula == null ? '' : String(celula).trim();
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

        // Filtros de texto continuam usando o filtro nativo do DataTables.
        document.addEventListener('input', function (event) {
            const input = event.target.closest('.filtro-coluna');
            if (!input || input.dataset.tabela !== idTabela) return;

            const coluna = Number(input.dataset.coluna);
            if (!Number.isInteger(coluna)) return;

            if (colunasNumericas.includes(coluna)) {
                // O filtro numérico é tratado pelo ext.search abaixo.
                dt.draw();
            } else {
                dt.column(coluna).search(input.value).draw();
            }
        });

        // Filtro numérico com operadores: >, >=, <, <= e =.
        // Sem operador, o valor é tratado como igualdade.
        if (DataTable.ext && DataTable.ext.search) {
            DataTable.ext.search.push(function (settings, data) {
                if (settings.nTable !== tabela) return true;

                for (const coluna of colunasNumericas) {
                    const input = document.querySelector(
                        '.filtro-coluna[data-tabela="' + CSS.escape(idTabela) + '"][data-coluna="' + coluna + '"]'
                    );

                    if (!input || !input.value.trim()) continue;

                    const filtro = interpretarFiltroNumerico(input.value);
                    if (filtro.invalido) return false;

                    const numero = Number(obterTextoCelula(data[coluna]).replace(/\s/g, '').replace(',', '.'));
                    if (!Number.isFinite(numero) || !compararNumero(numero, filtro)) return false;
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