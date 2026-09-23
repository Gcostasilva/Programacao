<?php

require_once __DIR__ . '/BaseModel.php';

class EstornoModel extends BaseModel
{
    public function registrar(array $dados): bool
    {
        date_default_timezone_set('America/Sao_Paulo');

        $sql = "INSERT INTO estornos
                    (pedido, atendimento, total_parcial, motivo, data_estorno)
                VALUES
                    (:pedido, :atendimento, :total_parcial, :motivo, :data_estorno)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':pedido' => $dados['pedido'],
            ':atendimento' => $dados['atendimento'],
            ':total_parcial' => $dados['total_parcial'],
            ':motivo' => $dados['motivo'],
            ':data_estorno' => date('Y-m-d H:i:s')
        ]);
    }
}
