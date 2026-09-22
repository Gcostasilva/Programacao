<?php

require_once __DIR__ . '/../models/ProdutoModel.php';

$model = new ProdutoModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    try {
        if ($acao === 'salvar') {
            $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;
            $codigo = trim((string) ($_POST['codigo'] ?? ''));
            $descricao = trim((string) ($_POST['descricao'] ?? ''));
            $grupo = trim((string) ($_POST['grupo'] ?? ''));
            $especial = ((int) ($_POST['especial'] ?? 0)) === 1 ? 1 : 0;
            $pesoLiquido = (float) str_replace(',', '.', (string) ($_POST['peso_liquido'] ?? 0));
            $espessura = (float) str_replace(',', '.', (string) ($_POST['espessura'] ?? 0));
            $ativo = ((int) ($_POST['ativo'] ?? 0)) === 1 ? 1 : 0;

            if ($codigo === '' || $descricao === '') {
                throw new RuntimeException('Código e descrição são obrigatórios.');
            }
            if ($pesoLiquido < 0 || $espessura < 0) {
                throw new RuntimeException('Peso líquido e espessura não podem ser negativos.');
            }
            if ($model->existeCodigo($codigo, $id)) {
                throw new RuntimeException('Já existe um produto com este código.');
            }

            $model->salvar($id, $codigo, $descricao, $grupo, $especial, $pesoLiquido, $espessura, $ativo);
            header('Location: index.php?page=acos&status=salvo');
            exit;
        }

        if ($acao === 'excluir') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new RuntimeException('Registro inválido.');
            }

            $produto = $model->buscarPorId($id);
            if (!$produto) {
                throw new RuntimeException('Produto não encontrado.');
            }

            $referencias = $model->contarReferencias($produto['codigo']);
            if ($referencias > 0) {
                throw new RuntimeException("Não é possível excluir o aço/produto: existem {$referencias} registro(s) já cadastrados utilizando este código. Desative-o em vez de excluir.");
            }

            $model->excluir($id);
            header('Location: index.php?page=acos&status=excluido');
            exit;
        }
    } catch (Throwable $e) {
        $mensagem = urlencode($e->getMessage());
        header("Location: index.php?page=acos&status=erro&msg={$mensagem}");
        exit;
    }
}

$registros = $model->listarCadastro();
include __DIR__ . '/../pages/cadastros/acos/index.php';
