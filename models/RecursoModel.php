<?php

require_once __DIR__ . '/BaseModel.php';

class RecursoModel extends BaseModel
{
    public function listarDiario()
    {
        $sql = "SELECT * FROM maquinas where tipo = 'diaria'";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function listarSemanal()
    {
        $sql = "SELECT * FROM maquinas where tipo = 'semanal'";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}