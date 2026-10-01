<?php

require_once __DIR__ . '/BaseModel.php';

class CadastroMotivoEstornoModel extends BaseModel
{
    public function listar(): array
    {
        return $this->pdo->query(
            "SELECT id, motivo, ativo
             FROM motivos_estorno
             ORDER BY motivo"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarAtivos(): array
    {
        return $this->pdo->query(
            "SELECT id, motivo
             FROM motivos_estorno
             WHERE ativo = 1
             ORDER BY motivo"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, motivo, ativo
             FROM motivos_estorno
             WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function salvar(?int $id, string $motivo, int $ativo): void
    {
        if ($id === null) {
            $stmt = $this->pdo->prepare(
                "INSERT INTO motivos_estorno (motivo, ativo)
                 VALUES (:motivo, :ativo)"
            );
        } else {
            $stmt = $this->pdo->prepare(
                "UPDATE motivos_estorno
                 SET motivo = :motivo, ativo = :ativo
                 WHERE id = :id"
            );
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        }

        $stmt->bindValue(':motivo', $motivo);
        $stmt->bindValue(':ativo', $ativo, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function existeMotivo(string $motivo, ?int $id = null): bool
    {
        $sql = "SELECT COUNT(*)
                FROM motivos_estorno
                WHERE UPPER(TRIM(motivo)) = UPPER(TRIM(:motivo))";

        if ($id !== null) {
            $sql .= " AND id <> :id";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':motivo', $motivo);

        if ($id !== null) {
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        }

        $stmt->execute();
        return (int) $stmt->fetchColumn() > 0;
    }

    public function contarReferencias(string $motivo): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*)
             FROM estornos
             WHERE UPPER(TRIM(motivo)) = UPPER(TRIM(:motivo))"
        );
        $stmt->execute([':motivo' => $motivo]);

        return (int) $stmt->fetchColumn();
    }

    public function excluir(int $id): void
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM motivos_estorno
             WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);
    }
}
