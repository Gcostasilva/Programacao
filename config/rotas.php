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
// semanal -------------------------------------
Router::add(
    'prog_diaria_editar',
    'pages/programacao/diaria/editar.php',
    'Editar Programação'
);
Router::add(
    'prog_semanal',
    'pages/programacao/semanal/index.php',
    'Programação Semanal'
);

Router::add(
    'prog_semanal_novo',
    'pages/programacao/semanal/novo.php',
    'Nova Programação Semanal'
);

Router::add(
    'prog_semanal_editar',
    'pages/programacao/semanal/editar.php',
    'Editar Programação Semanal'
);
// quinzenal -------------------------------------
Router::add(
    'prog_quinzenal',
    'pages/programacao/quinzena/index.php',
    'Programação Quinzenal'
);