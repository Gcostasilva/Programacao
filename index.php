<?php
ob_start(); 
require 'config/router.php';
$router = new Router();

// Rotas que devolvem apenas JSON (endpoints chamados via fetch/AJAX)
$rotasAjax = [
    'prog_diaria_buscar',
    'prog_diaria_baixar',
    'prog_diaria_baixa_salvar',
    'prog_diaria_excluir',
    'prog_diaria_editar',
    // vá adicionando aqui outras rotas "buscar" que você criar
    // ex: 'prog_semanal_buscar', 'corte_dobra_buscar'...
];

$paginaAtual = $_GET['page'] ?? 'dashboard';

if (in_array($paginaAtual, $rotasAjax)) {
    $router->carregar();
    exit;
}
?>

<!DOCTYPE html>
<html>
<?php include 'includes/head.php'; ?>
<body class="layout-fixed sidebar-expand-lg">
    <div class="app-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        <?php include 'includes/navbar.php'; ?>
        <main class="app-main">
            <?php $router->carregar(); ?>
        </main>
        <?php include 'includes/footer.php'; ?>
    </div>
    <?php include 'includes/scripts.php'; ?>
<?php ob_end_flush(); ?>
</body>
</html>