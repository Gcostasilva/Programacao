<?php

require_once __DIR__ . '/../models/ProgramacaoModel.php';

class ProgramacaoController
{
    public function index()
    {
        $model = new ProgramacaoModel();

        $programacoes = $model->listar($_POST);

        require __DIR__ . '/../pages/programacao/diaria/index.php';
    }

    public function salvar()
    {
        $model = new ProgramacaoModel();

        $model->salvar($_POST);

        header("Location: index.php?page=programacao/diaria&msg=salvo");
        exit;
    }


}