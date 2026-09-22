<?php

require_once __DIR__ . '/../models/CadastroMaquinaModel.php';

$model = new CadastroMaquinaModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    try {
        if ($acao === 'salvar') {
            $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;
            $descricao = trim((string) ($_POST['descricao'] ?? ''));
            $ativo = ((int) ($_POST['ativo'] ?? 0)) === 1 ? 1 : 0;
            $tipo = trim((string) ($_POST['tipo'] ?? ''));
            $capacidade = (int) ($_POST['capacidade'] ?? 0);

            if ($descricao === '') {
                throw new RuntimeException('Informe a descrição do equipamento.');
            }
            if ($capacidade < 0) {
                throw new RuntimeException('A capacidade não pode ser negativa.');
            }
            if ($model->existeDescricao($descricao, $id)) {
                throw new RuntimeException('Já existe um equipamento com esta descrição.');
            }

            $model->salvar($id, $descricao, $ativo, $tipo, $capacidade);
            header('Location: index.php?page=equipamentos&status=salvo');
            exit;
        }

        if ($acao === 'excluir') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new RuntimeException('Registro inválido.');
            }

            $referencias = $model->contarReferencias($id);
            if ($referencias > 0) {
                throw new RuntimeException("Não é possível excluir: o equipamento possui {$referencias} registro(s) de programação vinculado(s). Desative-o em vez de excluir.");
            }

            $model->excluir($id);
            header('Location: index.php?page=equipamentos&status=excluido');
            exit;
        }
    } catch (Throwable $e) {
        $mensagem = urlencode($e->getMessage());
        header("Location: index.php?page=equipamentos&status=erro&msg={$mensagem}");
        exit;
    }
}

$registros = $model->listar();
include __DIR__ . '/../pages/cadastros/equipamento/index.php';
