<?php

require_once __DIR__ . '/BaseModel.php';

/**
 * Acesso aos dados necessários para os relatórios de programação.
 *
 * Este model não monta HTML/PDF e não contém regras de apresentação.
 * Ele entrega somente os dados brutos já relacionados e ordenados.
 */
class RelatorioProgramacaoModel extends BaseModel
{
    /**
     * Retorna a programação do período informado, opcionalmente filtrada
     * por equipamento.
     *
     * @return array<int, array<string, mixed>>
     */
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
                pr.peca_realizada AS pecas_realizadas,
                pr.peso_realizado AS peso_realizado,
                pr.obs AS observacao,
                pr.ordem AS ordem
            FROM programacao AS pr
            LEFT JOIN maquinas AS mq
                ON pr.maquina_id = mq.id
            LEFT JOIN produtos AS pd
                ON pr.produto_id = pd.codigo
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

        $sql .= "
            ORDER BY
                pr.maquina_id,
                pr.data,
                pr.ordem,
                pr.id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
