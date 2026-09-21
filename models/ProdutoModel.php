<?php

require_once __DIR__ . '/BaseModel.php';

class ProdutoModel extends BaseModel
{
    public function listarCodigosProgramacao(): array
    {
        $sql = "SELECT codigo, descricao, peso_liquido
                FROM produtos
                WHERE codigo LIKE '04%' OR codigo LIKE '05%'
                ORDER BY codigo";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar(string $codigo): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM produtos WHERE codigo = :codigo");
        $stmt->execute([':codigo' => $codigo]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);
        return $produto ?: null;
    }
}
