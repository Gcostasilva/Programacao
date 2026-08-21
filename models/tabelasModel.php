<?php

require_once __DIR__ . '/BaseModel.php';

class tabelasModel extends BaseModel
{
    public function listarTabDiario(bool $exibirBaixados = false)
    {

    $condicaoBaixados = $exibirBaixados ? '1=1' : 'pr.peso_realizado = 0';
        $sql = "
                SELECT
        pr.id           as id,
        DATE_FORMAT(pr.data, '%d/%m/%Y')            as data,
        vd.nome         as vendedor,
        mq.descricao    as recurso,
        pr.pedido       as pedido,
        pr.espessura    as espessura,
        pr.aco          as aco,
        pr.peso         as peso,
        pr.peso_realizado   as peso_realizado,
        pr.obs          as obs,
        pr.falta_mp      as falta_mp,
        pr.ordem         as ordem

        from PROGRAMACAO pr

        left join maquinas mq
        on pr.maquina_id = mq.id

        left join vendedores vd
        on pr.vendedor = vd.id

        where pr.espessura is not null
        and (
            data = CURDATE()
            or (data <> CURDATE() and {$condicaoBaixados})
        )
        
        ORDER BY falta_mp, pr.data, pr.ordem

        limit 50

        ";


        $stmt = $this->pdo->prepare($sql);

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function listarTabSemanal()
    {
        $sql = "SELECT 

        pr.id           as id,
        pr.demanda      as demanda,
        pr.produto_id   as produto_id,
        vd.descricao     as descricao,
        pr.qtd          as qtd,
        pr.peso         as peso,
        pr.peca_realizada as peca_realizada,
        pr.peso_realizado as peso_realizado,
        pr.obs  as obs,
        mq.descricao    as recurso,
        pr.maquina_id    as maquina_id,
        pr.data         as data
        
        FROM programacao as pr

        LEFT JOIN maquinas as mq
        ON pr.maquina_id = mq.id
        
        LEFT JOIN produtos as vd
        ON pr.produto_id = vd.codigo
        
        where produto_id is not null
        
        ORDER BY ordem

        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}