<?php

require_once 'router.php';

Router::add('index', 'index.php', 'inicio');
Router::add('demanda', 'pages/demanda/index.php', 'Demanda');
Router::add('pedidos', 'controllers/PedidosController.php', 'Pedidos na Industria');
Router::add('importacao', 'pages/importacao/index.php', 'Importação');
Router::add('relatorios', 'pages/relatorios/index.php', 'Relatórios');
Router::add('relatorio_programacao', 'pages/relatorios/programacao.php', 'Relatório de Programação');
Router::add('relatorio_programacao_quinzenal', 'pages/relatorios/programacao_quinzenal.php', 'Relatório de Programação Quinzenal');
Router::add('relatorio_programacao_diaria', 'pages/relatorios/programacao_diaria.php', 'Relatório de Programação Diária');
Router::add('relatorio_programacao_dados', 'controllers/RelatorioProgramacao_Controller.php', 'Dados do Relatório de Programação');
Router::add('dashboard', 'pages/dashboard/index.php', 'Dashboard');

// cadastros -------------------------------------
// diaria -------------------------------------
Router::add('prog_diaria', 'controllers/ProgDiária_Controller.php', 'Programação Diária');
Router::add('prog_diaria_salvar', 'pages/programacao/diaria/salvar.php', 'Nova Programação');
Router::add('prog_diaria_baixar', 'pages/programacao/diaria/baixa.php', 'Baixa');
Router::add('prog_diaria_buscar', 'pages/programacao/diaria/buscar.php', 'Editar Programação');
Router::add('prog_diaria_baixa_salvar', 'pages/programacao/diaria/baixa_salvar.php', 'Editar Programação');
Router::add('prog_diaria_excluir', 'pages/programacao/diaria/excluir.php', 'Excluir Programação');
Router::add('prog_diaria_reordenar', 'pages/programacao/diaria/reordenar.php', 'Reordenar Programação Diária');
Router::add('prog_diaria_editar', 'pages/programacao/diaria/editar.php', 'Reordenar Programação Diária');
Router::add('prog_diaria_filtrar', 'pages/programacao/diaria/filtrar.php', 'Filtrar Programação Diária');
Router::add('prog_diaria_dados', 'pages/programacao/diaria/dados.php', 'Filtrar Programação Diária');

// semanal -------------------------------------
Router::add('prog_semanal', 'controllers/ProgSemanal_Controller.php', 'Programação Semanal');
Router::add('prog_semanal_novo', 'pages/programacao/semanal/novo.php', 'Nova Programação Semanal');
Router::add('prog_semanal_buscar', 'pages/programacao/semanal/buscar.php', 'Buscar Programação Semanal');
Router::add('prog_semanal_buscarCodigo', 'pages/programacao/semanal/buscarProduto.php', 'Buscar Código');
Router::add('prog_semanal_editar', 'pages/programacao/semanal/editar.php', 'Editar Programação Semanal');
Router::add('prog_semanal_excluir', 'pages/programacao/semanal/excluir.php', 'Excluir Programação Semanal');
Router::add('prog_semanal_reordenar', 'pages/programacao/semanal/reordenar.php', 'Reordenar Programação Semanal');
Router::add('prog_semanal_filtrar', 'pages/programacao/semanal/prog_semanal_filtrar.php', 'Filtrar Programação Semanal');
Router::add('prog_semanal_indicadores', 'pages/programacao/semanal/indicadores.php', 'Filtrar Programação Semanal');

// quinzenal -------------------------------------
Router::add('prog_quinzenal', 'controllers/ProgQuinzenal_Controller.php', 'Programação Quinzenal');
Router::add('prog_quinzenal_salvar', 'pages/programacao/quinzena/salvar.php', 'Nova Programação Quinzenal');
Router::add('prog_quinzenal_editar', 'pages/programacao/quinzena/editar.php', 'Editar Programação Quinzenal');
Router::add('prog_quinzenal_excluir', 'pages/programacao/quinzena/excluir.php', 'Excluir Programação Quinzenal');
Router::add('prog_quinzenal_reordenar', 'pages/programacao/quinzena/reordenar.php', 'Reordenar Programação Quinzenal');

Router::add('temas', 'pages/temas.php', 'Selecione seu tema');

// pedidos -------------------------------------------
Router::add('pedidos_salvar', 'pages/pedidos/salvar.php', 'Pedidos na industria');
Router::add('pedidos_buscar', 'pages/pedidos/buscar.php', 'Consultar entradas do pedido');
Router::add('pedidos_comentario', 'pages/pedidos/comentario.php', 'Comentários do pedido');
Router::add('pedidos_estorno', 'pages/pedidos/estorno.php', 'Registrar estorno do pedido');

// Cadastros ------------------------------------------
Router::add('cadastro_aco', 'controllers/CadastroAcosController.php', 'Cadastro de Tipo de Aço');
Router::add('cadastro_vendedores', 'controllers/CadastroVendedoresController.php', 'Cadastro de Vendedores');
Router::add('cadastro_equipamentos', 'controllers/CadastroMaquinasController.php', 'Cadastro de equipamentos'); 