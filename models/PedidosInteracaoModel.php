<?php

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../config/usuario.php';

class PedidosInteracaoModel extends BaseModel
{
    public function buscarEntradas(string $pedido): array
    {
        $sql = "SELECT p.*,
                    (SELECT COUNT(*) FROM pedidos_industria_comentarios c
                     WHERE c.pedido_industria_id = p.id) AS total_comentarios,
                    (SELECT COUNT(*) FROM estornos e
                     WHERE e.pedido = p.pedido) AS total_estornos,
                    CASE
                        WHEN p.saida IS NOT NULL
                         AND p.saida <> '0000-00-00 00:00:00'
                         AND NOT EXISTS (
                             SELECT 1 FROM pedidos_industria posterior
                             WHERE posterior.pedido = p.pedido
                               AND posterior.tipo = p.tipo
                               AND posterior.entrada > p.saida
                         )
                        THEN 1 ELSE 0
                    END AS pode_cancelar_saida,
                    CASE
                        WHEN p.saida IS NULL
                         AND NOT EXISTS (
                             SELECT 1 FROM pedidos_industria posterior
                             WHERE posterior.pedido = p.pedido
                               AND posterior.tipo = p.tipo
                               AND posterior.entrada > p.entrada
                         )
                        THEN 1 ELSE 0
                    END AS pode_excluir_entrada
                FROM pedidos_industria p
                WHERE p.pedido = :pedido
                ORDER BY p.tipo, p.entrada";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':pedido' => $pedido]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function adicionarComentario(int $pedidoId, string $comentario): bool
    {
        $comentario = trim($comentario);
        if ($comentario === '') {
            return false;
        }

        $usuario = usuarioAtual();
        $sql = "INSERT INTO pedidos_industria_comentarios
                    (pedido_industria_id, comentario, usuario)
                VALUES (:pedido_id, :comentario, :usuario)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':pedido_id' => $pedidoId,
            ':comentario' => $comentario,
            ':usuario' => $usuario
        ]);
    }

    public function listarComentarios(int $pedidoId): array
    {
        $sql = "SELECT id, comentario, usuario,
                       DATE_FORMAT(criado_em, '%d/%m/%Y %H:%i') AS criado_em
                FROM pedidos_industria_comentarios
                WHERE pedido_industria_id = :pedido_id
                ORDER BY id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':pedido_id' => $pedidoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cancelarSaida(int $id): array
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                "SELECT id, pedido, tipo, entrada, saida
                 FROM pedidos_industria WHERE id = :id FOR UPDATE"
            );
            $stmt->execute([':id' => $id]);
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$registro) {
                throw new DomainException('A entrada não foi encontrada.');
            }
            if (empty($registro['saida']) || $registro['saida'] === '0000-00-00 00:00:00') {
                throw new DomainException('Este registro não possui saída para cancelar.');
            }

            $stmt = $this->pdo->prepare(
                "SELECT 1 FROM pedidos_industria
                 WHERE pedido = :pedido AND tipo = :tipo AND entrada > :saida
                 LIMIT 1"
            );
            $stmt->execute([
                ':pedido' => $registro['pedido'],
                ':tipo' => $registro['tipo'],
                ':saida' => $registro['saida']
            ]);
            if ($stmt->fetchColumn()) {
                throw new DomainException('Não é possível cancelar a saída: existe uma entrada posterior para o mesmo pedido e tipo.');
            }

            $stmt = $this->pdo->prepare(
                "UPDATE pedidos_industria SET saida = NULL, user_saida = NULL
                 WHERE id = :id AND saida IS NOT NULL"
            );
            $stmt->execute([':id' => $id]);
            if ($stmt->rowCount() !== 1) {
                throw new DomainException('A saída não pôde ser cancelada. Atualize a consulta e tente novamente.');
            }

            $this->pdo->commit();
            return ['sucesso' => true];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function excluirEntrada(int $id): array
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                "SELECT id, pedido, tipo, entrada, saida
                 FROM pedidos_industria WHERE id = :id FOR UPDATE"
            );
            $stmt->execute([':id' => $id]);
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$registro) {
                throw new DomainException('A entrada não foi encontrada.');
            }
            if (!empty($registro['saida']) && $registro['saida'] !== '0000-00-00 00:00:00') {
                throw new DomainException('Não é possível excluir uma entrada que já possui saída.');
            }

            $stmt = $this->pdo->prepare(
                "SELECT 1 FROM pedidos_industria
                 WHERE pedido = :pedido AND tipo = :tipo AND entrada > :entrada
                 LIMIT 1"
            );
            $stmt->execute([
                ':pedido' => $registro['pedido'],
                ':tipo' => $registro['tipo'],
                ':entrada' => $registro['entrada']
            ]);
            if ($stmt->fetchColumn()) {
                throw new DomainException('Não é possível excluir: existe uma entrada posterior para o mesmo pedido e tipo.');
            }

            // Remove comentários vinculados para que a entrada seja excluída por completo.
            $stmt = $this->pdo->prepare(
                "DELETE FROM pedidos_industria_comentarios WHERE pedido_industria_id = :id"
            );
            $stmt->execute([':id' => $id]);

            $stmt = $this->pdo->prepare(
                "DELETE FROM pedidos_industria WHERE id = :id AND saida IS NULL"
            );
            $stmt->execute([':id' => $id]);
            if ($stmt->rowCount() !== 1) {
                throw new DomainException('A entrada não pôde ser excluída. Atualize a consulta e tente novamente.');
            }

            $this->pdo->commit();
            return ['sucesso' => true];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

}
