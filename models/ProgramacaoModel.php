<?php

require_once __DIR__ . '/BaseModel.php';

class ProgramacaoModel extends BaseModel
{

    public function salvar(array $dados): bool
    {

        try {

            $this->pdo->beginTransaction();

            $sql = "

                INSERT INTO programacao(

                    maquina_id,
                    data,
                    pedido,
                    espessura,
                    aco,
                    vendedor,
                    peso,
                    obs

                )

                VALUES(

                    :recurso,
                    :data_programacao,
                    :pedido,
                    :espessura,
                    :aco,
                    :vendedor,
                    :peso,
                    :observacao

                )

            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([

                ':recurso' => $dados['recurso'],
                ':data_programacao' => $dados['data'],
                ':pedido' => $dados['pedido'],
                ':espessura' => str_replace(',', '.', $dados['espessura']),
                ':aco' => $dados['aco'],
                ':vendedor' => $dados['vendedor'],
                ':peso' => $dados['peso'],
                ':observacao' => $dados['observacao']

            ]);

            $this->pdo->commit();

            return true;

        } catch (PDOException $e) {

            $this->pdo->rollBack();

            throw $e;

        }

    }
    public function excluir_prog(int $id)
    {
        try {

            $this->pdo->beginTransaction();
            $sql = ' DELETE from programacao WHERE id = :id ';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            $this->pdo->commit();
        } catch (PDOException $e) {
            throw $e;
        }
    }
    public function buscarPorId(int $id)
    {
        try {
            $sql = "SELECT * FROM programacao WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $erro) {
            throw $erro;
        }
    }
    public function buscarPorPedido(int $pedido)
    {
        $sql = "
            SELECT
                pr.id                   as id,
                pr.maquina_id           as recurso_id,
                mq.descricao            as recurso,
                pr.data                 as data,
                pr.pedido               as pedido,
                pr.espessura            as espessura,
                pr.aco                  as aco,
                pr.vendedor             as vendedor_id,
                vd.nome                 as vendedor,
                pr.peso                 as peso,
                pr.peso_realizado       as peso_real,
                pr.obs                  as obs
            FROM programacao pr
            LEFT JOIN maquinas mq
            on pr.maquina_id = mq.id

            LEFT JOIN vendedores vd
            on pr.vendedor = vd.id

            WHERE pedido = :pedido
            ORDER BY data
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':pedido' => $pedido]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function atualizar(array $dados): bool
    {
        try {

            $this->pdo->beginTransaction();

            $sql = "
            UPDATE programacao SET

                maquina_id = :recurso,
                data = :data_programacao,
                pedido = :pedido,
                espessura = :espessura,
                aco = :aco,
                vendedor = :vendedor,
                peso = :peso,
                obs = :observacao

            WHERE id = :id
        ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([

                ':id' => $dados['id'],
                ':recurso' => $dados['recurso'],
                ':data_programacao' => $dados['data'],
                ':pedido' => $dados['pedido'],
                ':espessura' => $dados['espessura'],
                ':aco' => $dados['aco'],
                ':vendedor' => $dados['vendedor'],
                ':peso' => $dados['peso'],
                ':observacao' => $dados['observacao']

            ]);

            $this->pdo->commit();

            return true;

        } catch (PDOException $e) {

            $this->pdo->rollBack();

            throw $e;

        }
    }
    /**
     * Grava o peso real de uma linha específica (identificada pelo id),
     * usada na tela de "Baixar Programação".
     */
    public function atualizarPesoReal(array $dados): bool
    {
        $sql = "
            UPDATE programacao
            SET peso_realizado = :peso_real
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $dados['id'],
            ':peso_real' => $dados['peso_real'],
        ]);
    }

}