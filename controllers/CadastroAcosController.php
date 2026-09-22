<?php

require_once __DIR__ . '/../models/CadastroAcoModel.php';

$model = new CadastroAcoModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    try {
        if ($acao === 'salvar') {
            $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;
            $tipo = trim((string) ($_POST['tipo'] ?? ''));
            $ativo = ((int) ($_POST['ativo'] ?? 0)) === 1 ? 1 : 0;

            if ($tipo === '') {
                throw new RuntimeException('Informe o tipo de aço.');
            }

            if ($model->existeTipo($tipo, $id)) {
                throw new RuntimeException('Já existe um tipo de aço com essa descrição.');
            }

            $model->salvar($id, $tipo, $ativo);

            header('Location: index.php?page=cadastro_aco&status=salvo');
            exit;
        }

        if ($acao === 'excluir') {
            $id = (int) ($_POST['id'] ?? 0);

            if ($id <= 0) {
                throw new RuntimeException('Registro inválido.');
            }

            $aco = $model->buscar($id);

            if (!$aco) {
                throw new RuntimeException('Tipo de aço não encontrado.');
            }

            $referencias = $model->contarReferencias($aco['tipo']);

            if ($referencias > 0) {
                throw new RuntimeException(
                    "Não é possível excluir este tipo de aço: existem {$referencias} programação(ões) vinculada(s). Desative-o em vez de excluir."
                );
            }

            $model->excluir($id);

            header('Location: index.php?page=cadastro_aco&status=excluido');
            exit;
        }
    } catch (Throwable $e) {
        $mensagem = urlencode($e->getMessage());
        header("Location: index.php?page=cadastro_aco&status=erro&msg={$mensagem}");
        exit;
    }
}

$registros = $model->listar();

include __DIR__ . '/../pages/cadastros/aco/index.php';
