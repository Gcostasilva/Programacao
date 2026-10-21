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

    public function listarProdDiaria(string $equipamento, string $data)
    {
        $sql = " SELECT 
        sum(pr.peso) as peso,
        sum(pr.peso_realizado) as peso_realizado,
        sum(pr.peso) - sum(pr.peso_realizado) as saldo,
        sum(pr.peso) / mq.capacidade as utilizacao,
        mq.capacidade as capacidade

        from programacao pr

        LEFT JOIN maquinas mq
        on pr.maquina_id = mq.id

        WHERE pr.maquina_id = :equipamento and pr.data = :data
        ";
        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':equipamento' => $equipamento,
            ':data' => $data
        ]);

        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $resultado;
    }

    public function listarTabSemanal(string $dataInicio, string $dataFim, ?int $recursoId = null)
    {
        $sql = "        SELECT 
            pr.id AS id,
            pr.demanda AS demanda,
            pr.produto_id AS produto_id,
            CONCAT(
            COALESCE(vd.descricao,''), 
            COALESCE(pr.desc_complementar, '')
            ) AS descricao,
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

    public function listarProdSemanal(string $equipamento, string $data_INICIO, string $data_FIM)
    {
        $sql = " SELECT 
        pr.data                                             as data,
        sum(pr.peso)                                        as peso,
        sum(pr.peso_realizado)                              as peso_realizado,
        sum(pr.peso) - sum(pr.peso_realizado)               as saldo,
        (sum(pr.peso) / NULLIF(mq.capacidade, 0))*100       as utilizacao,
        mq.capacidade                                       as capacidade

        from programacao pr

        LEFT JOIN maquinas mq
        on pr.maquina_id = mq.id

        WHERE pr.maquina_id = :equipamento and pr.data BETWEEN :data_inicio AND :data_fim

        GROUP BY pr.data

        ";
        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':equipamento' => $equipamento,
            ':data_inicio' => $data_INICIO,
            ':data_fim' => $data_FIM
        ]);

        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $resultado;
    }

    public function listaCodigos()
    {
        try {
            $sql = "SELECT codigo, descricao, peso_liquido FROM produtos where descricao LIKE '%DESB*%' OR descricao LIKE '%PROCESSO%' ORDER BY grupo, descricao, codigo";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $erro) {
            throw $erro;
        }
    }

    public function tabelaPedidos()
    {
        try {
            $sql = "SELECT
                        pi.*,
                        CASE pi.tipo
                            WHEN 'P' THEN 'Padrão'
                            WHEN 'E' THEN 'Encomenda'
                            WHEN 'T' THEN 'Telha'
                            ELSE pi.tipo
                        END AS tipo_nome
                    FROM pedidos_industria pi
                    ORDER BY pi.id DESC
                    LIMIT 100";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $hoje = new DateTimeImmutable('today');

            foreach ($pedidos as &$pedido) {
                // Pedido com saída ou sem previsão não possui prazo em aberto.
                if (!empty($pedido['saida']) && $pedido['saida'] !== '0000-00-00 00:00:00') {
                    $pedido['prazo'] = null;
                    continue;
                }

                $valorPrevisao = trim((string) ($pedido['previsao'] ?? ''));
                if ($valorPrevisao === '' || $valorPrevisao === '0000-00-00' || $valorPrevisao === '0000-00-00 00:00:00') {
                    $pedido['prazo'] = '';
                    continue;
                }

                try {
                    $previsao = new DateTimeImmutable(substr($valorPrevisao, 0, 10));
                } catch (Throwable $e) {
                    $pedido['prazo'] = '';
                    continue;
                }

                $diasUteis = 0;
                if ($previsao > $hoje) {
                    // Conta os dias úteis após hoje até a previsão, incluindo a data prevista.
                    for ($dia = $hoje->modify('+1 day'); $dia <= $previsao; $dia = $dia->modify('+1 day')) {
                        if ((int) $dia->format('N') <= 5) {
                            $diasUteis++;
                        }
                    }

                    if ($diasUteis === 0) {
                        $pedido['prazo'] = 'Fim de semana';
                    } else {
                        $pedido['prazo'] = $diasUteis . ($diasUteis === 1 ? ' dia útil' : ' dias úteis');
                    }
                } elseif ($previsao < $hoje) {
                    // Para atraso, conta apenas dias úteis entre a previsão e ontem.
                    for ($dia = $previsao; $dia < $hoje; $dia = $dia->modify('+1 day')) {
                        if ((int) $dia->format('N') <= 5) {
                            $diasUteis++;
                        }
                    }

                    $pedido['prazo'] = $diasUteis . ($diasUteis === 1 ? ' dia útil vencido' : ' dias úteis vencidos');
                } else {
                    $pedido['prazo'] = ((int) $hoje->format('N') <= 5) ? 'Hoje' : 'Fim de semana';
                }
            }
            unset($pedido);

            return $pedidos;
        } catch (PDOException $erro) {
            throw $erro;
        }
    }

    public function tabelaPedidos_filtrado(string $pedido)
    {
        try {
            $sql = "SELECT * FROM pedidos_industria where pedido = :pedido order by entrada";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':pedido' => $pedido]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $erro) {
            throw $erro;
        }
    }
}
