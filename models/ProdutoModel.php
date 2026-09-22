<?php

require_once __DIR__ . '/BaseModel.php';

class ProdutoModel extends BaseModel
{
    /** Produtos utilizados na programação semanal: DESB e PROCESSO. */
    public function listarCodigosProgramacao(): array
    {
        $sql = "SELECT codigo, descricao, peso_liquido
                FROM produtos
                WHERE (descricao LIKE '%DESB*%' OR descricao LIKE '%PROCESSO%')

                ORDER BY grupo, descricao, codigo";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Produtos disponíveis para a programação quinzenal: códigos iniciados em 04 ou 05. */
    public function listarCodigosQuinzenal(): array
    {
        $sql = "SELECT codigo, descricao, peso_liquido
                FROM produtos
                WHERE (codigo LIKE '04%' OR codigo LIKE '05%')
       
                ORDER BY grupo, descricao, codigo";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar(string $codigo): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM produtos WHERE codigo = :codigo");
        $stmt->execute([':codigo' => $codigo]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function listarCadastro(): array
    {
        return $this->pdo->query(
            "SELECT id, codigo, descricao, grupo, especial, peso_liquido, espessura
             FROM produtos
             ORDER BY codigo"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function salvar(?int $id, string $codigo, string $descricao, ?string $grupo, int $especial, float $pesoLiquido, float $espessura, int $ativo): void
    {
        if ($id === null) {
            $stmt = $this->pdo->prepare(
                "INSERT INTO produtos (codigo, descricao, grupo, especial, peso_liquido, espessura, ativo)
                 VALUES (:codigo, :descricao, :grupo, :especial, :peso_liquido, :espessura, :ativo)"
            );
        } else {
            $stmt = $this->pdo->prepare(
                "UPDATE produtos
                 SET codigo = :codigo, descricao = :descricao, grupo = :grupo,
                     especial = :especial, peso_liquido = :peso_liquido,
                     espessura = :espessura, ativo = :ativo
                 WHERE id = :id"
            );
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        }

        $stmt->bindValue(':codigo', $codigo);
        $stmt->bindValue(':descricao', $descricao);
        $stmt->bindValue(':grupo', $grupo !== '' ? $grupo : null, $grupo !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':especial', $especial, PDO::PARAM_INT);
        $stmt->bindValue(':peso_liquido', $pesoLiquido);
        $stmt->bindValue(':espessura', $espessura);
        $stmt->bindValue(':ativo', $ativo, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function existeCodigo(string $codigo, ?int $id = null): bool
    {
        $sql = "SELECT COUNT(*) FROM produtos WHERE codigo = :codigo";
        if ($id !== null) {
            $sql .= " AND id <> :id";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':codigo', $codigo);
        if ($id !== null) {
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        }
        $stmt->execute();
        return (int) $stmt->fetchColumn() > 0;
    }

    public function contarReferencias(string $codigo): int
    {
        $sql = "SELECT
                    (SELECT COUNT(*) FROM programacao WHERE produto_id = :codigo) +
                    (SELECT COUNT(*) FROM demanda WHERE codigo = :codigo) +
                    (SELECT COUNT(*) FROM tb_previsao WHERE codigo = :codigo)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':codigo' => $codigo]);
        return (int) $stmt->fetchColumn();
    }

    public function excluir(int $id): void
    {
        $produto = $this->buscarPorId($id);
        if (!$produto) {
            return;
        }

        $stmt = $this->pdo->prepare("DELETE FROM produtos WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM produtos WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
