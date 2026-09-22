<?php

require_once __DIR__ . '/BaseModel.php';

class ProgramacaoQuinzenalModel extends BaseModel
{
    public function garantirEstrutura(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS programacao_quinzenal (id INT UNSIGNED NOT NULL AUTO_INCREMENT, quinzena VARCHAR(8) NOT NULL, produto_id VARCHAR(50) NOT NULL, quantidade DECIMAL(15,3) NOT NULL DEFAULT 0, peca_realizada DECIMAL(15,3) NOT NULL DEFAULT 0, ordem_producao VARCHAR(50) NULL, obs TEXT NULL, ordem INT NOT NULL DEFAULT 0, criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (id), INDEX idx_quinzena (quinzena), INDEX idx_produto (produto_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $this->pdo->exec($sql);

        $stmt = $this->pdo->query("SHOW COLUMNS FROM programacao_quinzenal LIKE 'quinzena'");
        $coluna = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($coluna && preg_match('/varchar\\((\\d+)\\)/i', $coluna['Type'], $match) && (int)$match[1] < 8) {
            $this->pdo->exec("ALTER TABLE programacao_quinzenal MODIFY quinzena VARCHAR(8) NOT NULL");
        }

        $stmt = $this->pdo->query("SHOW COLUMNS FROM programacao_quinzenal LIKE 'ordem'");
        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->pdo->exec("ALTER TABLE programacao_quinzenal ADD COLUMN ordem INT NOT NULL DEFAULT 0 AFTER obs");
        }
    }

    public function listar(?string $quinzena = null): array
    {
        $this->garantirEstrutura();
        $sql = "SELECT q.id, q.quinzena, q.produto_id, COALESCE(p.descricao, '') AS descricao, COALESCE(p.peso_liquido, 0) AS peso_liquido, q.quantidade, q.peca_realizada, q.ordem_producao, q.obs, q.ordem FROM programacao_quinzenal q LEFT JOIN produtos p ON p.codigo = q.produto_id";
        $params = [];
        if ($quinzena !== null && $quinzena !== '') {
            $sql .= ' WHERE q.quinzena = :quinzena';
            $params[':quinzena'] = $quinzena;
        }
        $sql .= ' ORDER BY q.quinzena DESC, q.ordem ASC, q.id ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function reordenar(string $quinzena, array $itens): void
    {
        $this->garantirEstrutura();
        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])-[12]$/', $quinzena)) throw new InvalidArgumentException('Quinzena inválida.');
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare('UPDATE programacao_quinzenal SET ordem = :ordem WHERE id = :id AND quinzena = :quinzena');
            foreach ($itens as $posicao => $item) {
                $id = (int)($item['id'] ?? 0);
                if ($id > 0) $stmt->execute([':ordem' => $posicao, ':id' => $id, ':quinzena' => $quinzena]);
            }
            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function buscarPorId(int $id): ?array
    {
        $this->garantirEstrutura();
        $stmt = $this->pdo->prepare("SELECT q.*, COALESCE(p.descricao, '') AS descricao, COALESCE(p.peso_liquido, 0) AS peso_liquido FROM programacao_quinzenal q LEFT JOIN produtos p ON p.codigo = q.produto_id WHERE q.id = :id");
        $stmt->execute([':id' => $id]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ?: null;
    }

    public function salvar(array $dados): int
    {
        $this->garantirEstrutura();
        $this->validar($dados);
        $stmtMax = $this->pdo->prepare('SELECT COALESCE(MAX(ordem), -1) + 1 FROM programacao_quinzenal WHERE quinzena = :quinzena');
        $stmtMax->execute([':quinzena' => $dados['quinzena']]);
        $ordem = (int)$stmtMax->fetchColumn();
        $stmt = $this->pdo->prepare('INSERT INTO programacao_quinzenal (quinzena, produto_id, quantidade, peca_realizada, ordem_producao, obs, ordem) VALUES (:quinzena, :produto_id, :quantidade, :peca_realizada, :ordem_producao, :obs, :ordem)');
        $stmt->execute([':quinzena'=>$dados['quinzena'], ':produto_id'=>$dados['produto_id'], ':quantidade'=>$dados['quantidade'], ':peca_realizada'=>$dados['peca_realizada'] ?? 0, ':ordem_producao'=>$dados['ordem_producao'] ?? null, ':obs'=>$dados['obs'] ?? null, ':ordem'=>$ordem]);
        return (int)$this->pdo->lastInsertId();
    }

    public function atualizar(array $dados): bool
    {
        $this->garantirEstrutura();
        $this->validar($dados);
        $stmt = $this->pdo->prepare("UPDATE programacao_quinzenal SET quinzena=:quinzena, produto_id=:produto_id, quantidade=:quantidade, peca_realizada=:peca_realizada, ordem_producao=:ordem_producao, obs=:obs WHERE id=:id");
        return $stmt->execute([':id'=>(int)$dados['id'], ':quinzena'=>$dados['quinzena'], ':produto_id'=>$dados['produto_id'], ':quantidade'=>$dados['quantidade'], ':peca_realizada'=>$dados['peca_realizada'] ?? 0, ':ordem_producao'=>$dados['ordem_producao'] ?? null, ':obs'=>$dados['obs'] ?? null]);
    }

    public function excluir(int $id): bool
    {
        $this->garantirEstrutura();
        $stmt = $this->pdo->prepare('DELETE FROM programacao_quinzenal WHERE id = :id');
        return $stmt->execute([':id'=>$id]);
    }

    private function validar(array $dados): void
    {
        if (empty($dados['quinzena']) || !preg_match('/^\d{4}-(0[1-9]|1[0-2])-[12]$/', $dados['quinzena'])) throw new InvalidArgumentException('Quinzena inválida.');
        if (empty($dados['produto_id'])) throw new InvalidArgumentException('Informe o produto.');
        if (!is_numeric($dados['quantidade']) || (float)$dados['quantidade'] <= 0) throw new InvalidArgumentException('A quantidade deve ser maior que zero.');
        if (isset($dados['peca_realizada']) && (!is_numeric($dados['peca_realizada']) || (float)$dados['peca_realizada'] < 0)) throw new InvalidArgumentException('A quantidade produzida não pode ser negativa.');
    }
}
