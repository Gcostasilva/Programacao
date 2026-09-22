<?php
require_once 'config/menu.php';

$paginaAtual = $_GET['page'] ?? '';
?>

<aside class="app-sidebar shadow">

    <div class="sidebar-brand border-0 align-self-center align-content-center ps-md-3">
        <a href="index.php" class="brand-link">
            <img src="uploads/img/Logotipo_eg.png" alt="Perfinasa Metais" class="brand-image opacity-75 shadow">
            <span class="brand-text fw-bold">Perfinasa Metais</span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column"
                data-lte-toggle="treeview"
                data-accordion="true">

                <?php foreach ($MENU as $item): ?>

                    <?php
                    $submenuAtivo = false;

                    if (isset($item['submenu'])) {
                        foreach ($item['submenu'] as $sub) {
                            if (empty($sub['url'])) {
                                continue;
                            }

                            parse_str(
                                parse_url($sub['url'], PHP_URL_QUERY) ?? '',
                                $parametros
                            );

                            if (($parametros['page'] ?? '') === $paginaAtual) {
                                $submenuAtivo = true;
                                break;
                            }
                        }
                    }
                    ?>

                    <?php if (isset($item['submenu'])): ?>

                        <li class="nav-item <?= $submenuAtivo ? 'menu-open' : '' ?>">
                            <a href="<?= htmlspecialchars($item['url'] ?? '#', ENT_QUOTES, 'UTF-8') ?>"
                               class="nav-link <?= $submenuAtivo ? 'active' : '' ?>">
                                <i class="nav-icon <?= htmlspecialchars($item['icone'], ENT_QUOTES, 'UTF-8') ?>"></i>
                                <p>
                                    <?= htmlspecialchars($item['titulo'], ENT_QUOTES, 'UTF-8') ?>
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>

                            <ul class="nav nav-treeview">
                                <?php foreach ($item['submenu'] as $sub): ?>

                                    <?php
                                    $subAtivo = false;

                                    if (!empty($sub['url'])) {
                                        parse_str(
                                            parse_url($sub['url'], PHP_URL_QUERY) ?? '',
                                            $parametrosSub
                                        );

                                        $subAtivo = (($parametrosSub['page'] ?? '') === $paginaAtual);
                                    }
                                    ?>

                                    <li class="nav-item">
                                        <a href="<?= htmlspecialchars($sub['url'] ?? '#', ENT_QUOTES, 'UTF-8') ?>"
                                           class="nav-link <?= $subAtivo ? 'active' : '' ?>">
                                            <i class="nav-icon <?= htmlspecialchars($SUB['icone'], ENT_QUOTES, 'UTF-8') ?>"></i>
                                            <p><?= htmlspecialchars($sub['titulo'], ENT_QUOTES, 'UTF-8') ?></p>
                                        </a>
                                    </li>

                                <?php endforeach; ?>
                            </ul>
                        </li>

                    <?php else: ?>

                        <?php
                        $itemAtivo = false;

                        if (!empty($item['url'])) {
                            parse_str(
                                parse_url($item['url'], PHP_URL_QUERY) ?? '',
                                $parametrosItem
                            );

                            $itemAtivo = (($parametrosItem['page'] ?? '') === $paginaAtual);
                        }
                        ?>

                        <li class="nav-item">
                            <a href="<?= htmlspecialchars($item['url'] ?? '#', ENT_QUOTES, 'UTF-8') ?>"
                               class="nav-link <?= $itemAtivo ? 'active' : '' ?>">
                                <i class="nav-icon BI <?= htmlspecialchars($item['icone'], ENT_QUOTES, 'UTF-8') ?>"></i>
                                <p><?= htmlspecialchars($item['titulo'], ENT_QUOTES, 'UTF-8') ?></p>
                            </a>
                        </li>

                    <?php endif; ?>

                <?php endforeach; ?>

            </ul>
        </nav>
    </div>

</aside>
