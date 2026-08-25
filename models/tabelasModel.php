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
    public function listarTabSemanal(string $dataInicio,string $dataFim,?int $recursoId = null) {
        $sql = "        SELECT 
            pr.id AS id,
            pr.demanda AS demanda,
            pr.produto_id AS produto_id,
            vd.descricao AS descricao,
            pr.qtd AS qtd,
            pr.peso AS peso,
            pr.peca_realizada AS peca_realizada,
            pr.peso_realizado AS peso_realizado,
            pr.obs AS obs,
            mq.descricao AS recurso,
            pr.maquina_id AS maquina_id,
            pr.data AS data

        FROM programacao AS pr

        LEFT JOIN maquinas AS mq
            ON pr.maquina_id = mq.id

        LEFT JOIN produtos AS vd
            ON pr.produto_id = vd.codigo

        WHERE pr.produto_id IS NOT NULL
          AND pr.data BETWEEN :data_inicio AND :data_fim
    ";

        // Adiciona o filtro somente quando um recurso foi informado
        if ($recursoId !== null) {
            $sql .= " AND pr.maquina_id = :recurso_id ";
        }

        $sql .= " ORDER BY pr.ordem ";

        $stmt = $this->pdo->prepare($sql);

        $params = [
            ':data_inicio' => $dataInicio,
            ':data_fim' => $dataFim
        ];

        if ($recursoId !== null) {
            $params[':recurso_id'] = $recursoId;
        }

        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}