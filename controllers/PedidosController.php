<?php

require_once __DIR__ .'/../models/tabelasModel.php';

$tabela_pedidos = new tabelasModel();

$tabela = [];

$tabela['tabPedidos'] = $tabela_pedidos->tabelaPedidos();


include __DIR__ . '/../pages/pedidos/index.php';