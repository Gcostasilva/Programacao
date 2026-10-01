<?php

require_once __DIR__ . '/../models/CadastroMotivoEstornoModel.php';

$model = new CadastroMotivoEstornoModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    try {
        if ($acao === 'salvar') {
            $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;
            $motivo = trim((string) ($_POST['motivo'] ?? ''));
            $ativo = ((int) ($_POST['ativo'] ?? 0)) === 1 ? 1 : 0;

            if ($motivo === '') {
                throw new RuntimeException('Informe o motivo do estorno.');
            }

            if (strlen($motivo) > 100) {
                throw new RuntimeException('O motivo deve ter no máximo 100 caracteres.');
            }

            if ($model->existeMotivo($motivo, $id)) {
                throw new RuntimeException('Já existe um motivo de estorno com essa descrição.');
            }

            $model->salvar($id, $motivo, $ativo);
            header('Location: index.php?page=cadastro_motivos_estorno&status=salvo');
            exit;
        }

        if ($acao === 'excluir') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new RuntimeException('Registro inválido.');
            }

            $registro = $model->buscar($id);
            if (!$registro) {
                throw new RuntimeException('Motivo de estorno não encontrado.');
            }

            $referencias = $model->contarReferencias($registro['motivo']);
            if ($referencias > 0) {
                throw new RuntimeException(
                    "Não é possível excluir este motivo: existem {$referencias} estorno(s) vinculado(s). Desative-o em vez de excluir."
                );
            }

            $model->excluir($id);
            header('Location: index.php?page=cadastro_motivos_estorno&status=excluido');
            exit;
        }
    } catch (Throwable $e) {
        header('Location: index.php?page=cadastro_motivos_estorno&status=erro&msg=' . urlencode($e->getMessage()));
        exit;
    }
}

$registros = $model->listar();
include __DIR__ . '/../pages/cadastros/motivos_estorno/index.php';
