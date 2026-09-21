<?php
require_once 'c:\\xampp\\htdocs\\Programacao\\config\\banco.php';

$sql = "
SELECT
    dm.codigo,
    prd.descricao,
    dm.armazem,
    dm.estoque,
    dm.pendencia AS pedido,
    (dm.estoque - dm.pendencia) AS saldo,
    pr.quantidade AS previsao,
    CASE WHEN pr.quantidade IS NULL OR pr.quantidade = 0 THEN 0
         ELSE ROUND(((dm.estoque - dm.pendencia) / pr.quantidade) * 30, 0) END AS dias_estoque,
    GREATEST(IFNULL(pr.quantidade,0) - (dm.estoque - dm.pendencia), 0) AS necessidade,
    IFNULL(pg.op_aberta, 0) AS op_aberta
FROM demanda dm
LEFT JOIN produtos prd ON dm.codigo = prd.codigo
LEFT JOIN tb_previsao pr ON dm.codigo = pr.codigo AND dm.armazem = pr.armazem
LEFT JOIN (
    SELECT produto_id, SUM(qtd) AS op_aberta
    FROM programacao
    WHERE produto_id IS NOT NULL
    GROUP BY produto_id
    HAVING SUM(COALESCE(peca_realizada, 0)) = 0
       AND SUM(COALESCE(qtd, 0)) > 0
) pg ON dm.codigo = pg.produto_id
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$demanda = $stmt->fetchAll(PDO::FETCH_ASSOC);

$modoDemanda = $modoDemanda ?? 'pagina';
$idTabelaDemanda = $idTabelaDemanda ?? 'tb_demanda';
$modoSelecao = $modoDemanda === 'selecao';
?>

<div class="container-fluid">
    <div class="table-responsive">
        <table id="<?= htmlspecialchars($idTabelaDemanda) ?>" class="table table-hover table-striped align-middle" style="font-size:.8em;width:100%">
            <thead>
                <tr>
                    <th>Código</th><th>Descrição</th><th>Amz</th><th>Estoque</th><th>Pendência</th><th>Saldo</th><th>Previsão</th><th>Dias Estoque</th><th>Necessidade</th><th>OP Aberta</th>
                    <?php if ($modoSelecao): ?><th>Selecionar</th><?php endif; ?>
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
                        <?php if ($modoSelecao): ?><td><button type="button" class="btn btn-sm btn-primary btn-selecionar-demanda" data-codigo="<?= htmlspecialchars($d['codigo'], ENT_QUOTES) ?>"><i class="bi bi-check-lg"></i></button></td><?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
(function(){
    const tabela=document.getElementById(<?= json_encode($idTabelaDemanda) ?>);
    if(!tabela||typeof DataTable==='undefined')return;
    if(tabela.dataset.dt==='1')return;
    new DataTable(tabela,{paging:false,scrollY:'60vh',scrollCollapse:true,language:{search:'Pesquisar:',zeroRecords:'Nenhum registro encontrado',emptyTable:'Nenhum registro disponível',info:'_TOTAL_ registros'},columnDefs:[<?php if($modoSelecao): ?>{targets:-1,orderable:false,searchable:false}<?php endif; ?>]});
    tabela.dataset.dt='1';
    document.addEventListener('click',function(e){
        const b=e.target.closest('.btn-selecionar-demanda');
        if(!b)return;
        const campo=document.querySelector(window.demandaCampoDestino||'#codigo');
        if(!campo)return;
        campo.value=b.dataset.codigo;
        campo.dispatchEvent(new Event('input',{bubbles:true}));
        campo.dispatchEvent(new Event('change',{bubbles:true}));
        const modal=b.closest('.modal');
        if(modal)bootstrap.Modal.getOrCreateInstance(modal).hide();
    });
})();
</script>
