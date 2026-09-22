<?php
require_once 'config/menu.php';

$paginaAtual = $_GET['page'] ?? '';
?>

<aside class="app-sidebar shadow">

    <div class="sidebar-brand border-0 align-self-center align-content-center ps-md-3">

        <a href="index.php" class="brand-link">
            <img src="uploads/img/Logotipo_eg

            <span class=" brand-text fw-bold">
            Perfinasa Metais
            </span>
        </a>

    </div>

    <div class="sidebar-wrapper">

        <nav class="mt-2">

            <ul class="nav sidebar-menu flex-column"
                data-lte-toggle="treeview"
                data-accordion="true">

                <?php foreach ($MENU as $item): ?>
                    <?php foreach ($MENU as $item): ?>

                        <?php
                        $submenuAtivo = false;

                        if (isset($item['submenu'])) {

                            foreach ($item['submenu'] as $sub) {

                                parse_str(
                                    parse_url($sub['url'], PHP_URL_QUERY),
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

                                #">

                                <i class="nav-icon <?= $item['icone'] ?>"></i>

                                <p>
                                    <?= $item['titulo'] ?>
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>

                                </a>

                                <ul class="nav nav-treeview">

                                    <?php foreach ($item['submenu'] as $sub): ?>

                                        <?php
                                        parse_str(
                                            parse_url($sub['url'], PHP_URL_QUERY),
                                            $parametrosSub
                                        );

                                        $subAtivo =
                                            (($parametrosSub['page'] ?? '') === $paginaAtual);
                                        ?>

                                        <li class="nav-item">

                                            <?= $sub['url'] ?> class="nav-link <?= $subAtivo ? 'active' : '' ?>">

                                            <i class="nav-icon bi bi-circle"></i>

                                            <p><?= $sub['titulo'] ?></p>

                                            </a>

                                        </li>

                                    <?php endforeach; ?>

                                </ul>

                            </li>

                        <?php else: ?>

                            <li class="nav-item">

                                <?= $item['url'] ?> class="nav-link">

                                <i class="nav-icon <?= $item['icone'] ?>"></i>

                                <p><?= $item['titulo'] ?></p>

                                </a>

                            </li>

                        <?php endif; ?>

                    <?php endforeach; ?>

                <?php endforeach; ?>

            </ul>

        </nav>

    </div>

</aside>