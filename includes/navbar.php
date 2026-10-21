<nav class="app-header navbar navbar-expand shadow hidden-print">
    <ul class="navbar-nav hidden-print">
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center" data-lte-toggle="sidebar" href="#" role="button"
                aria-label="Toggle sidebar">
                <i class="bi bi-list h3  m-0"></i>
                <span class="h3 text-body m-0 ps-3" style="line-height: 30px;"><?= $router->titulo(); ?></span>
            </a>
        </li>
    </ul>
    <ul class="navbar-nav ms-auto align-items-center">
        <?php if (function_exists('authUsuario') && authUsuario()): ?>
        <li class="nav-item d-none d-md-block"><span class="nav-link small text-secondary"><?= htmlspecialchars(authUsuario()['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars(['admin'=>'Administrador','analista_pcp'=>'Analista PCP','visitante'=>'Visitante'][authUsuario()['perfil'] ?? ''] ?? '', ENT_QUOTES, 'UTF-8') ?></span></li>
        <li class="nav-item"><a class="nav-link" href="index.php?page=logout" title="Sair"><i class="bi bi-box-arrow-right"></i> Sair</a></li>
        <?php endif; ?>
    </ul>
</nav>