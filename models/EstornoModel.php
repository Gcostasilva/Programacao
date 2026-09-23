<?php

require_once __DIR__ . '/BaseModel.php';

class EstornoModel extends BaseModel
{
    public function registrar(array $dados): bool
    {
        date_default_timezone_set('America/Sao_Paulo');

        $sql = "INSERT INTO estornos
                    (pedido, atendimento, vendedor_id, total_parcial, motivo, data_estorno)
                VALUES
                    (:pedido, :atendimento, :vendedor_id, :total_parcial, :motivo, :data_estorno)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':pedido' => $dados['pedido'],
            ':atendimento' => $dados['atendimento'],
            ':vendedor_id' => $dados['vendedor_id'],
            ':total_parcial' => $dados['total_parcial'],
            ':motivo' => $dados['motivo'],
            ':data_estorno' => date('Y-m-d H:i:s')
        ]);
    }

    public function listarVendedores(): array
    {
        return $this->pdo->query(
            "SELECT id, nome, ativo FROM vendedores ORDER BY nome"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarPorPedido(string $pedido): array
    {
        $sql = "SELECT
                    e.id,
                    e.pedido,
                    e.atendimento,
                    COALESCE(v.nome, 'Vendedor não informado') AS vendedor,
                    e.total_parcial,
                    e.motivo,
                    e.data_estorno
                FROM estornos e
                LEFT JOIN vendedores v ON v.id = e.vendedor_id
                WHERE e.pedido = :pedido
                ORDER BY e.data_estorno DESC, e.id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':pedido' => $pedido]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarRelatorio(array $filtros = []): array
    {
        $where = [];
        $params = [];
        $limit = [];

        if (($filtros['pedido'] ?? '') !== '') {
            $where[] = 'e.pedido = :pedido';
            $params[':pedido'] = $filtros['pedido'];
        }
        if (!empty($filtros['vendedor_id'])) {
            $where[] = 'e.vendedor_id = :vendedor_id';
            $params[':vendedor_id'] = (int) $filtros['vendedor_id'];
        }
        if (!empty($filtros['tipo'])) {
            $where[] = 'e.total_parcial = :tipo';
            $params[':tipo'] = $filtros['tipo'];
        }
        if (!empty($filtros['data_inicio'])) {
            $where[] = 'DATE(e.data_estorno) >= :data_inicio';
            $params[':data_inicio'] = $filtros['data_inicio'];
        }
        if (!empty($filtros['data_fim'])) {
            $where[] = 'DATE(e.data_estorno) <= :data_fim';
            $params[':data_fim'] = $filtros['data_fim'];
        }
        if (empty($filtros['data_fim']) && empty($filtros['data_inicio'])){
            $limit[] = 'limit 100';
        }

        $sql = "SELECT
                    e.id,
                    e.pedido,
                    e.atendimento,
                    e.vendedor_id,
                    COALESCE(v.nome, 'Vendedor não informado') AS vendedor,
                    e.total_parcial,
                    e.motivo,
                    e.data_estorno
                FROM estornos e
                LEFT JOIN vendedores v ON v.id = e.vendedor_id";

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY e.data_estorno DESC, e.id DESC ';

        if ($limit) {
            $sql .= implode(' ', $limit);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
