<?php

require_once __DIR__ . '/BaseModel.php';

class PedidoModel extends BaseModel
{
    public function salvar(array $dados): bool
    {
        try {
            date_default_timezone_set('America/Sao_Paulo');
            $pedido = $dados['pedido'];
            $tipo = $dados['tipo'];
            $data_hora = date('Y/m/d H:i:s');
            $data = $dados['data'];
            $user = getenv('USERNAME'); // alterar está buscando o usuariop do servidor
            

            // Busca o registro diretamente
            $sql = 'SELECT id FROM pedidos_industria WHERE pedido = :pedido AND tipo = :tipo AND saida IS NULL';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':pedido' => $pedido,
                ':tipo' => $tipo
            ]);

            // Obtém o registro (retorna array associativo ou false)
            $REG = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->pdo->beginTransaction();

            if (!$REG) { 
                // Registro não existe: Faz a INCLUSÃO
                $sql = "INSERT INTO pedidos_industria (
                            pedido,
                            tipo,
                            previsao,
                            entrada,
                            user_entrada
                        ) VALUES (
                            :pedido,
                            :tipo,
                            :previsao,
                            :data_hora,
                            :user
                        )";

                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    ':pedido'    => $pedido,
                    ':tipo'      => $tipo,
                    ':previsao'  => $data,
                    ':data_hora' => $data_hora,
                    ':user'      => $user
                ]);
            } else { 
                // Registro existe: Faz o UPDATE usando o ID capturado no fetch
                $id = $REG['id']; // garanta que no banco a coluna é 'id' (ou 'ID')

                $sql = "UPDATE pedidos_industria SET  
                            saida = :data_hora,
                            user_saida = :user
                        WHERE id = :id";

                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    ':data_hora' => $data_hora,
                    ':user'      => $user,
                    ':id'        => $id
                ]);
            }

            $this->pdo->commit();
            return true;

        } catch (PDOException $e) {
            // Cancela a transação apenas se houver uma ativa
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }
}