<?php

require_once __DIR__ . '/../models/CadastroVendedorModel.php';

$model = new CadastroVendedorModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    try {
        if ($acao === 'salvar') {
            $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;
            $nome = trim((string) ($_POST['nome'] ?? ''));
            $ativo = ((int) ($_POST['ativo'] ?? 0)) === 1 ? 1 : 0;

            if ($nome === '') {
                throw new RuntimeException('Informe o nome do vendedor.');
            }

            if ($model->existeNome($nome, $id)) {
                throw new RuntimeException('Já existe um vendedor com este nome.');
            }

            $model->salvar($id, $nome, $ativo);
            header('Location: index.php?page=cadastro_vendedores');
            exit;
        }

        if ($acao === 'excluir') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new RuntimeException('Registro inválido.');
            }

            $referencias = $model->contarReferencias($id);
            if ($referencias > 0) {
                // Definimos a mensagem que queremos exibir
                $mensagem = "Não é possível excluir: o vendedor possui {$referencias} registro(s) de programação vinculado(s). Desative-o em vez de excluir.";

                // Gera o alerta JavaScript e redireciona
                echo "<script>
                    alert('" . addslashes($mensagem) . "');
                    window.location.href = 'index.php?page=cadastro_vendedores';
                </script>";
                exit;
            }

            $model->excluir($id);
            header('Location: index.php?page=cadastro_vendedores');
            exit;
        }
    } catch (Throwable $e) {
        $mensagem = urlencode($e->getMessage());

        header("Location: index.php?page=vendedores&status=erro&msg={$mensagem}");
        exit;
    }
}

$registros = $model->listar();
include __DIR__ . '/../pages/cadastros/vendedor/index.php';
