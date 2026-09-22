<?php

require_once __DIR__ . '/BaseModel.php';

class CadastroVendedorModel extends BaseModel
{
    public function listar(): array
    {
        return $this->pdo->query(
            "SELECT id, nome, ativo FROM vendedores ORDER BY nome"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT id, nome, ativo FROM vendedores WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function salvar(?int $id, string $nome, int $ativo): void
    {
        if ($id === null) {
            $stmt = $this->pdo->prepare(
                "INSERT INTO vendedores (nome, ativo) VALUES (:nome, :ativo)"
            );
        } else {
            $stmt = $this->pdo->prepare(
                "UPDATE vendedores SET nome = :nome, ativo = :ativo WHERE id = :id"
            );
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        }

        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':ativo', $ativo, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function existeNome(string $nome, ?int $id = null): bool
    {
        $sql = "SELECT COUNT(*) FROM vendedores WHERE UPPER(TRIM(nome)) = UPPER(TRIM(:nome))";
        if ($id !== null) {
            $sql .= " AND id <> :id";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':nome', $nome);
        if ($id !== null) {
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        }
        $stmt->execute();
        return (int) $stmt->fetchColumn() > 0;
    }

    public function contarReferencias(int $id): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM programacao WHERE vendedor = :id"
        );
        $stmt->bindValue(':id', (string) $id);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function excluir(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM vendedores WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }
}
