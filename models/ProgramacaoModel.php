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
                    falta_mp,
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
                    :falta_mp,
                    :observacao

                )

            ";

            $stmt = $this->pdo->prepare($sql);

            var_dump($dados);
            $stmt->execute([

                ':recurso' => $dados['recurso'],
                ':data_programacao' => $dados['data'],
                ':pedido' => $dados['pedido'],
                ':espessura' => str_replace(',', '.', $dados['espessura']),
                ':aco' => $dados['aco'],
                ':vendedor' => $dados['vendedor'],
                ':peso' => $dados['peso'],
                ':falta_mp' => isset($_POST['falta_mp']) ? 1 : 0,
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
            $sql = "SELECT 
                pr.id                   as id,
                pr.maquina_id           as maquina_id,
                mq.descricao            as maquina,
                pr.data                 as data_programacao,
                pr.pedido               as pedido,
                pr.espessura            as espessura,
                pr.aco                  as aco,
                pd.descricao            as descricao,
                pr.desc_complementar    as desc_complementar,
                pd.peso_liquido         as peso_liquido,
                pr.vendedor             as vendedor_id,
                vd.nome                 as vendedor,
                pr.qtd                  as qtd,
                pr.peso                 as peso,
                pr.peso_realizado       as peso_realizado,
                pr.obs                  as obs,
                pr.demanda              as demanda,
                pr.produto_id           as produto_id,
                pr.qtd                  as qtd,
                pr.peca_realizada       as peca_realizada 

            FROM programacao pr
            LEFT JOIN maquinas mq ON pr.maquina_id = mq.id
            LEFT JOIN vendedores vd ON pr.vendedor = vd.id
            LEFT JOIN produtos pd ON pr.produto_id = pd.codigo
            WHERE pr.id = :id";

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
                falta_mp = :falta_mp,
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
                ':falta_mp' => $dados['falta_mp'],
                ':observacao' => $dados['observacao']

            ]);

            $this->pdo->commit();

            return true;

        } catch (PDOException $e) {

            $this->pdo->rollBack();

            throw $e;

        }
    }
    public function atualizarOrdem(array $itens): bool
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "UPDATE programacao SET ordem = :ordem WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);

            foreach ($itens as $item) {
                $stmt->execute([
                    ':ordem' => $item['posicao'],
                    ':id' => $item['id'],
                ]);
            }

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






    public function salvar_semanal(array $dados): bool
    {

        try {

            $this->pdo->beginTransaction();

            $sql = "

                INSERT INTO programacao(
                    semana_id,
                    maquina_id,
                    data,
                    demanda,
                    produto_id,
                    desc_complementar,
                    qtd,
                    peso,
                    obs

                )

                VALUES(

                    :semana,
                    :recurso,
                    :data_prog,
                    :demanda,
                    :produto,
                    :complemento_descricao,
                    :qtd,
                    :peso,
                    :observacao

                )

            ";

            $stmt = $this->pdo->prepare($sql);

            var_dump($dados);
            $stmt->execute([

                ':semana' => $dados['semana'],
                ':recurso' => $dados['recurso'],
                ':data_prog' => $dados['data'],
                ':demanda' => $dados['demanda'],
                ':produto' => $dados['codigo'],
                'complemento_descricao' => $dados['complemento_descricao'],
                ':qtd' => $dados['quantidade'],
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
    public function atualizar_semanal(array $dados): bool
    {

        try {

            $this->pdo->beginTransaction();

            $sql = "

                UPDATE programacao
                SET
                    semana_id = :semana,
                    maquina_id = :recurso,
                    data = :data_prog,
                    demanda = :demanda,
                    produto_id = :produto,
                    desc_complementar = :descricao_COMP,
                    qtd = :qtd,
                    peso = :peso,
                    peca_realizada = :peca_realizada,
                    peso_realizado = :peso_realizado,
                    obs = :observacao
                WHERE id = :id
            ";

            $stmt = $this->pdo->prepare($sql);

            var_dump($dados);
            $stmt->execute([


                ':semana' => $dados['semana'],
                ':recurso' => $dados['recurso'],
                ':data_prog' => $dados['data'],
                ':demanda' => $dados['demanda'],
                ':produto' => $dados['codigo'],
                ':descricao_COMP' => $dados['descricao_COMP'],
                ':qtd' => $dados['quantidade'],
                ':peso' => $dados['peso'],
                ':peca_realizada' => $dados['peca_realizada'],
                ':peso_realizado' => $dados['peso_realizado'],
                ':observacao' => $dados['observacao'],
                ':id' => $dados['id']
            ]);
            error_log('ID recebido: ' . var_export($dados['id'], true));
            error_log('Linhas afetadas: ' . $stmt->rowCount());
            $this->pdo->commit();

            return true;

        } catch (PDOException $e) {

            $this->pdo->rollBack();

            throw $e;

        }

    }
    public function buscarPorCodigo(string $codigo)
    {
        try {
            $sql = "SELECT * FROM produtos WHERE codigo = :codigo";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':codigo' => $codigo]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $erro) {
            throw $erro;
        }
    }

    public function reordenarSemanal($itens)
    {

        $this->pdo->beginTransaction();

        $sql = "UPDATE programacao SET ordem = :ordem, data = :data WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        foreach ($itens as $item) {
            $stmt->execute([
                ':ordem' => $item['posicao'],
                ':data' => $item['data'],
                ':id' => $item['id']
            ]);
        }

        $this->pdo->commit();



    }

}