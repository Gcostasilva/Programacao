<?php

require_once __DIR__ . '/BaseModel.php';

class tabelasModel extends BaseModel
{
    public function listarTabDiario()
    {
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
        pr.obs          as obs

        from PROGRAMACAO pr

        left join maquinas mq
        on pr.maquina_id = mq.id

        left join vendedores vd
        on pr.vendedor = vd.id

        where pr.espessura is not null ";  /*and pr.peso_realizado = 0*/


        $stmt = $this->pdo->prepare($sql);

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function listarTabSemanal()
    {
        $sql = "SELECT * FROM maquinas where tipo = 'semanal'";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}