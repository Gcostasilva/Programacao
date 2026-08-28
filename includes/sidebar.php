<?php require_once 'config/menu.php'; ?>

<aside class="app-sidebar shadow" >

    <div class="sidebar-brand align-self-center">

        <a href="index.php" class="brand-link">
            <img src="uploads/img/Logotipo_pequeno.jpeg" class="brand-image opacity-75 shadow">
            <span class="brand-text fw-light fw-bold">Perfinasa Metais</span>
        </a>

    </div>

    <div class="sidebar-wrapper">

        <nav class="mt-2">

            <ul class="nav sidebar-menu flex-column"
                data-lte-toggle="treeview"
                data-accordion="false">

                <?php foreach($MENU as $item): ?>

                    <?php if(isset($item['submenu'])): ?>

                        <li class="nav-item menu-open">

                            <a href="#" class="nav-link">

                                <i class="nav-icon bi <?= $item['icone'] ?>"></i>

                                <p>

                                    <?= $item['titulo'] ?>

                                    <i class="nav-arrow bi bi-chevron-right"></i>

                                </p>

                            </a>

                            <ul class="nav nav-treeview">

                                <?php foreach($item['submenu'] as $sub): ?>

                                    <li class="nav-item">

                                        <a href="<?= $sub['url'] ?>" class="nav-link">

                                            <i class="nav-icon bi bi-circle"></i>

                                            <p><?= $sub['titulo'] ?></p>

                                        </a>

                                    </li>

                                <?php endforeach; ?>

                            </ul>

                        </li>

                    <?php else: ?>

                        <li class="nav-item">

                            <a href="<?= $item['url'] ?>" class="nav-link">

                                <i class="nav-icon bi <?= $item['icone'] ?>"></i>

                                <p><?= $item['titulo'] ?></p>

                            </a>

                        </li>

                    <?php endif; ?>

                <?php endforeach; ?>

            </ul>

        </nav>

    </div>

</aside>