<?php

require_once __DIR__ . '/BaseModel.php';

class ProdutoModel extends BaseModel
{
    /**
     * Produtos utilizados na programação semanal: DESB e PROCESSO.
     */
    public function listarCodigosProgramacao(): array
    {
        $sql = "SELECT codigo, descricao, peso_liquido
                FROM produtos
                WHERE descricao LIKE '%DESB*%'
                   OR descricao LIKE '%PROCESSO%'
                ORDER BY grupo, descricao, codigo";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Produtos disponíveis para a programação quinzenal: códigos iniciados em 04 ou 05.
     */
    public function listarCodigosQuinzenal(): array
    {
        $sql = "SELECT codigo, descricao, peso_liquido
                FROM produtos
                WHERE codigo LIKE '04%'
                   OR codigo LIKE '05%'
                ORDER BY grupo, descricao, codigo";

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
