<?php

require_once 'router.php';

Router::add(
    'index',
    'index.php',
    'inicio'
);
Router::add(
    'demanda',
    'pages/demanda/index.php',
    'Demanda'
);
Router::add(
    'pedidos',
    'pages/pedidos/index.php',
    'Pedidos na Industria'
);
Router::add(
    'importacao',
    'pages/importacao/index.php',
    'Importação'
);
Router::add(
    'relatorios',
    'pages/relatorios/index.php',
    'Relatórios'
);
Router::add(
    'dashboard',
    'pages/dashboard/index.php',
    'Dashboard'
);
// diaria -------------------------------------

Router::add(
    'prog_diaria',
    'controllers/ProgDiária_Controller.php',
    'Programação Diária'
);

Router::add(
    'prog_diaria_salvar',
    'pages/programacao/diaria/salvar.php',
    'Nova Programação'
);
Router::add(
    'prog_diaria_baixar',
    'pages/programacao/diaria/baixa.php',
    'Baixa'
);

Router::add(
    'prog_diaria_buscar',
    'pages/programacao/diaria/buscar.php',
    'Editar Programação'
);
Router::add(
    'prog_diaria_baixa_salvar',
    'pages/programacao/diaria/baixa_salvar.php',
    'Editar Programação'
);
Router::add(
    'prog_diaria_excluir',
    'pages/programacao/diaria/excluir.php',
    'Excluir Programação'
);
Router::add(
    'prog_diaria_reordenar',
    'pages/programacao/diaria/reordenar.php',
    'Reordenar Programação Diária'
);
Router::add(
    'prog_diaria_filtrar',
    'pages/programacao/diaria/filtrar.php',
    'Filtrar Programação Diária'
);
// semanal -------------------------------------
Router::add(
    'prog_semanal',
    'controllers/ProgSemanal_Controller.php',
    'Programação Semanal'
);
Router::add(
    'prog_semanal_novo',
    'pages/programacao/semanal/novo.php',
    'Nova Programação Semanal'
);
Router::add(
    'prog_semanal_buscar',
    'pages/programacao/semanal/buscar.php',
    'Buscar Programação Semanal'
);
Router::add(
    'prog_semanal_buscarCodigo',
    'pages/programacao/semanal/buscarProduto.php',
    'Buscar Código'
);

Router::add(
    'prog_semanal_editar',
    'pages/programacao/semanal/editar.php',
    'Editar Programação Semanal'
);
Router::add(
    'prog_semanal_excluir',
    'pages/programacao/semanal/excluir.php',
    'Excluir Programação Semanal'
);
Router::add(
    'prog_semanal_reordenar',
    'pages/programacao/semanal/reordenar.php',
    'Reordenar Programação Semanal'
);
Router::add(
    'prog_semanal_filtrar',
    'pages/programacao/semanal/prog_semanal_filtrar.php',
    'Filtrar Programação Semanal'
);

// quinzenal -------------------------------------
Router::add(
    'prog_quinzenal',
    'pages/programacao/quinzena/index.php',
    'Programação Quinzenal'
);