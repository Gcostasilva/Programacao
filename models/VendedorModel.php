<?php

require_once __DIR__ . '../BaseModel.php';

class VendedorModel extends BaseModel
{
    public function listar()
    {
        $sql = "SELECT * FROM vendedores ORDER BY nome";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}