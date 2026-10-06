<?php
ob_start();
require 'config/router.php';
$router = new Router();

// Rotas que devolvem apenas JSON (endpoints chamados via fetch/AJAX)
$rotasAjax = [
    'prog_diaria_buscar', 'prog_diaria_baixar', 'prog_diaria_baixa_salvar', 'prog_diaria_excluir',
    'prog_diaria_editar', 'prog_diaria_reordenar', 'prog_diaria_filtrar', 'prog_diaria_dados',
    'prog_semanal_buscar', 'prog_semanal_buscarCodigo', 'prog_semanal_reordenar', 'prog_semanal_excluir',
    'prog_semanal_editar', 'prog_semanal_filtrar', 'prog_quinzenal_reordenar',
    'salvar_pedido', 'pedidos_buscar', 'pedidos_comentario', 'pedidos_estorno', 'pedidos_estornos',
    'relatorio_estornos_excel', 'pedidos_motivos_estorno', 'pedidos_motivos_estorno_salvar', 'pedidos_motivos_estorno_buscar', 'pedidos_motivos_estorno_excluir'
];

$paginaAtual = $_GET['page'] ?? 'dashboard';

if (in_array($paginaAtual, $rotasAjax)) {
    $router->carregar();
    exit;
}
?>

<!DOCTYPE html>
<html language="pt-br" data-lte-primary="...">
<?php include 'includes/head.php'; ?>
<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse fixed-header">
    <div class="app-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        <?php include 'includes/navbar.php'; ?>
        <?php include 'includes/helpers.php'; ?>
        <main class="app-main">
            <?php $router->carregar(); ?>
        </main>
        <?php include 'includes/footer.php'; ?>
    </div>
    <?php include 'includes/scripts.php'; ?>
    <?php ob_end_flush(); ?>
</body>
</html>