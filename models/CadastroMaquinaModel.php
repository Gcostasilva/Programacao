<?php

require_once __DIR__ . '/BaseModel.php';

class CadastroMaquinaModel extends BaseModel
{
    public function listar(): array
    {
        return $this->pdo->query(
            "SELECT id, descricao, ativo, tipo, capacidade FROM maquinas ORDER BY descricao"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT id, descricao, ativo, tipo, capacidade FROM maquinas WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function salvar(?int $id, string $descricao, int $ativo, ?string $tipo, int $capacidade): void
    {
        if ($id === null) {
            $stmt = $this->pdo->prepare(
                "INSERT INTO maquinas (descricao, ativo, tipo, capacidade)
                 VALUES (:descricao, :ativo, :tipo, :capacidade)"
            );
        } else {
            $stmt = $this->pdo->prepare(
                "UPDATE maquinas
                 SET descricao = :descricao, ativo = :ativo, tipo = :tipo, capacidade = :capacidade
                 WHERE id = :id"
            );
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        }

        $stmt->bindValue(':descricao', $descricao);
        $stmt->bindValue(':ativo', $ativo, PDO::PARAM_INT);
        $stmt->bindValue(':tipo', $tipo !== '' ? $tipo : null, $tipo !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':capacidade', $capacidade, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function existeDescricao(string $descricao, ?int $id = null): bool
    {
        $sql = "SELECT COUNT(*) FROM maquinas WHERE UPPER(TRIM(descricao)) = UPPER(TRIM(:descricao))";
        if ($id !== null) {
            $sql .= " AND id <> :id";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':descricao', $descricao);
        if ($id !== null) {
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        }
        $stmt->execute();
        return (int) $stmt->fetchColumn() > 0;
    }

    public function contarReferencias(int $id): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM programacao WHERE maquina_id = :id"
        );
        $stmt->execute([':id' => $id]);
        return (int) $stmt->fetchColumn();
    }

    public function excluir(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM maquinas WHERE id = :id");
        $stmt->execute([':id' => $id]);
        
    }
}
