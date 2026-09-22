<?php

require_once __DIR__ . '/BaseModel.php';

class PedidosInteracaoModel extends BaseModel
{
    public function buscarEntradas(string $pedido): array
    {
        $sql = "SELECT p.*,
                    (SELECT COUNT(*) FROM pedidos_industria_comentarios c
                     WHERE c.pedido_industria_id = p.id) AS total_comentarios
                FROM pedidos_industria p
                WHERE p.pedido = :pedido
                ORDER BY p.entrada";
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

        $usuario = getenv('USERNAME') ?: ($_SERVER['REMOTE_USER'] ?? null);
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
}
