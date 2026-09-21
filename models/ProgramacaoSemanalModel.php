<?php

require_once __DIR__ . '/BaseModel.php';

class ProgramacaoSemanalModel extends BaseModel
{
    public function salvar(array $dados): bool
    {
        try {
            $this->pdo->beginTransaction();

            // A semana é determinada pela data da programação. A coluna
            // semana_id não é preenchida aqui porque os registros antigos
            // da programação também não dependem dela.
            $sql = "INSERT INTO programacao (
                        maquina_id,
                        data,
                        demanda,
                        produto_id,
                        desc_complementar,
                        qtd,
                        peso,
                        obs
                    ) VALUES (
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
                ':recurso' => $dados['recurso'],
                ':data_prog' => $dados['data'],
                ':demanda' => strtoupper($dados['demanda'] ?? ''),
                ':produto' => $dados['codigo'],
                ':complemento_descricao' => strtoupper($dados['complemento_descricao'] ?? ''),
                ':qtd' => $dados['quantidade'],
                ':peso' => ($dados['peso'] ?? '') !== '' ? $dados['peso'] : null,
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
