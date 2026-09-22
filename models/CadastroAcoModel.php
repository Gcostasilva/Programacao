<?php

require_once __DIR__ . '/BaseModel.php';

class CadastroAcoModel extends BaseModel
{
    public function listar(): array
    {
        return $this->pdo->query(
            "SELECT id, tipo, ativo
             FROM tipos_aco
             ORDER BY tipo"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarAtivos(): array
    {
        return $this->pdo->query(
            "SELECT id, tipo
             FROM tipos_aco
             WHERE ativo = 1
             ORDER BY tipo"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, tipo, ativo
             FROM tipos_aco
             WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function salvar(?int $id, string $tipo, int $ativo): void
    {
        if ($id === null) {
            $stmt = $this->pdo->prepare(
                "INSERT INTO tipos_aco (tipo, ativo)
                 VALUES (:tipo, :ativo)"
            );
        } else {
            $stmt = $this->pdo->prepare(
                "UPDATE tipos_aco
                 SET tipo = :tipo, ativo = :ativo
                 WHERE id = :id"
            );
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        }

        $stmt->bindValue(':tipo', $tipo);
        $stmt->bindValue(':ativo', $ativo, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function existeTipo(string $tipo, ?int $id = null): bool
    {
        $sql = "SELECT COUNT(*)
                FROM tipos_aco
                WHERE UPPER(TRIM(tipo)) = UPPER(TRIM(:tipo))";

        if ($id !== null) {
            $sql .= " AND id <> :id";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':tipo', $tipo);

        if ($id !== null) {
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        }

        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }

    public function contarReferencias(string $tipo): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*)
             FROM programacao
             WHERE UPPER(TRIM(aco)) = UPPER(TRIM(:tipo))"
        );
        $stmt->execute([':tipo' => $tipo]);

        return (int) $stmt->fetchColumn();
    }

    public function excluir(int $id): void
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM tipos_aco
             WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);
    }
}
