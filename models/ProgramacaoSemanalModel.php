<?php

require_once __DIR__ . '/BaseModel.php';

class ProgramacaoSemanalModel extends BaseModel
{
    public function salvar(array $dados): bool
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "INSERT INTO programacao (
                        semana_id,
                        maquina_id,
                        data,
                        demanda,
                        produto_id,
                        desc_complementar,
                        qtd,
                        peso,
                        obs
                    ) VALUES (
                        :semana,
                        :recurso,
                        :data_prog,
                        :demanda,
                        :produto,
                        :complemento_descricao,
                        :qtd,
                        :peso,
                        :observacao
                    )";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':semana' => $dados['semana'],
                ':recurso' => $dados['recurso'],
                ':data_prog' => $dados['data'],
                ':demanda' => strtoupper($dados['demanda']),
                ':produto' => $dados['codigo'],
                ':complemento_descricao' => strtoupper($dados['complemento_descricao'] ?? ''),
                ':qtd' => $dados['quantidade'],
                ':peso' => $dados['peso'] !== '' ? $dados['peso'] : null,
                ':observacao' => strtoupper($dados['observacao'] ?? '')
            ]);

            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
}
