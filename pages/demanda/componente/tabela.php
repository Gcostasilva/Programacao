<?php

require_once 'c:\xampp\htdocs\Programacao\config\banco.php';

$where = "";
$orderBy = "";

$sql = "

SELECT

    dm.codigo                            AS codigo,
    prd.descricao                        AS descricao,
    dm.armazem                           AS armazem,
    dm.estoque                           AS estoque,
    dm.pendencia                         AS pedido,
    (dm.estoque - dm.pendencia)          AS saldo,
    pr.quantidade                        AS previsao,

    CASE
        WHEN pr.quantidade IS NULL OR pr.quantidade = 0 THEN 0
        ELSE ROUND(((dm.estoque - dm.pendencia) / pr.quantidade) * 30, 0)
    END AS dias_estoque,

    GREATEST(IFNULL(pr.quantidade,0) -(dm.estoque - dm.pendencia),0) AS necessidade,
    IFNULL(pg.op_aberta,0)               AS op_aberta

FROM demanda dm

LEFT JOIN produtos prd
       ON dm.codigo = prd.codigo

LEFT JOIN tb_previsao pr
       ON dm.codigo = pr.codigo
      AND dm.armazem = pr.armazem

LEFT JOIN (

    SELECT

        produto_id, 
        SUM(qtd - peca_realizada) AS op_aberta

    FROM programacao

    where peca_realizada = 0

    GROUP BY
        produto_id

) pg

ON dm.codigo = pg.produto_id


$where

$orderBy

";
$stmt = $pdo->prepare($sql);

$stmt->execute();
$demanda = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <table id="tb_demanda" class="table hover" style="font-size: 0.8em;">
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
            </tr>
        </thead>
        <tbody>
            <?php foreach ($demanda as $d): ?>
                <tr>
                    <td> <?= $d['codigo'] ?></td>
                    <td> <?= $d['descricao'] ?></td>
                    <td> <?= $d['armazem'] ?></td>
                    <td> <?= $d['estoque'] ?></td>
                    <td> <?= $d['pedido'] ?></td>
                    <td> <?= $d['saldo'] ?></td>
                    <td> <?= $d['previsao'] ?></td>
                    <td> <?= $d['dias_estoque'] ?></td>
                    <td> <?= $d['necessidade'] ?></td>
                    <td>0</td>
                <?php endforeach; ?>
        </tbody>
    </table>
</div>


<script>new DataTable('#tb_demanda')</script>