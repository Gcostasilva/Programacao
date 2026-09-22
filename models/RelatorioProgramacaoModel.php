<?php

require_once __DIR__ . '/BaseModel.php';

/**
 * Acesso aos dados necessários para os relatórios de programação.
 */
class RelatorioProgramacaoModel extends BaseModel
{
    public function listarProgramacao(
        string $dataInicio,
        string $dataFim,
        ?int $recursoId = null
    ): array {
        $sql = "
            SELECT
                pr.id AS id,
                pr.data AS data,
                pr.maquina_id AS recurso_id,
                mq.descricao AS recurso,
                pr.demanda AS demanda,
                pr.produto_id AS produto_id,
                pd.descricao AS descricao_produto,
                pr.desc_complementar AS descricao_complementar,
                pr.qtd AS quantidade,
                pr.peso AS peso_estimado,
                pr.obs AS observacao,
                pr.ordem AS ordem
            FROM programacao AS pr
            LEFT JOIN maquinas AS mq ON pr.maquina_id = mq.id
            LEFT JOIN produtos AS pd ON pr.produto_id = pd.codigo
            WHERE pr.data BETWEEN :data_inicio AND :data_fim
              AND pr.produto_id IS NOT NULL
        ";

        $params = [
            ':data_inicio' => $dataInicio,
            ':data_fim' => $dataFim,
        ];

        if ($recursoId !== null) {
            $sql .= " AND pr.maquina_id = :recurso_id ";
            $params[':recurso_id'] = $recursoId;
        }

        $sql .= " ORDER BY pr.maquina_id, pr.data, pr.ordem, pr.id ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna exatamente os campos utilizados pela tabela da Programação Diária.
     */
    public function listarProgramacaoDiaria(
        string $data,
        ?int $recursoId = null
    ): array {
        $sql = "
            SELECT
                pr.id AS id,
                pr.data AS data,
                pr.maquina_id AS recurso_id,
                mq.descricao AS recurso,
                pr.pedido AS pedido,
                vd.nome AS vendedor,
                pr.espessura AS espessura,
                pr.aco AS aco,
                pr.peso AS peso,
                pr.peso_realizado AS peso_realizado,
                pr.obs AS observacao,
                pr.falta_mp AS falta_mp,
                pr.ordem AS ordem
            FROM PROGRAMACAO AS pr
            LEFT JOIN maquinas AS mq ON pr.maquina_id = mq.id
            LEFT JOIN vendedores AS vd ON pr.vendedor = vd.id
            WHERE pr.data = :data
              AND pr.espessura IS NOT NULL
        ";

        $params = [':data' => $data];

        if ($recursoId !== null) {
            $sql .= " AND pr.maquina_id = :recurso_id ";
            $params[':recurso_id'] = $recursoId;
        }

        $sql .= " ORDER BY pr.falta_mp, pr.ordem, pr.id ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Dados da Programação Quinzenal. Não existe equipamento nesta programação.
     * A origem é a mesma utilizada pela tela quinzenal: programacao_quinzenal + produtos.
     */
    public function listarProgramacaoQuinzenal(string $quinzena): array
    {
        $sql = "
            SELECT
                q.id AS id,
                q.quinzena AS quinzena,
                q.produto_id AS produto_id,
                COALESCE(p.descricao, '') AS descricao,
                COALESCE(p.peso_liquido, 0) AS peso_liquido,
                q.quantidade AS quantidade,
                q.peca_realizada AS peca_realizada,
                q.ordem_producao AS ordem_producao,
                q.obs AS observacao
            FROM programacao_quinzenal AS q
            LEFT JOIN produtos AS p ON p.codigo = q.produto_id
            WHERE q.quinzena = :quinzena
            ORDER BY q.id ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':quinzena' => $quinzena]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
