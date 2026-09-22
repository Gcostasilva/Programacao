<?php

$MENU = [



    [
        "titulo" => "Diária",
        "icone" => "bi bi-calendar-event",
        "url" => "index.php?page=prog_diaria"
    ],
    [
        "titulo" => "Semanal",
        "icone" => "bi bi-calendar-week",
        "url" => "index.php?page=prog_semanal"
    ],

    [
        "titulo" => "Quinzenal",
        "icone" => "bi bi-calendar2-range",
        "url" => "index.php?page=prog_quinzenal"
    ],

    [
        "titulo" => "Demanda",
        "icone" => "bi bi-table",
        "url" => "index.php?page=demanda"
    ],

    [
        "titulo" => "Pedidos na Indústria",
        "icone" => "bi bi-file-earmark-break-fill",
        "url" => "index.php?page=pedidos"
    ],

    [
        "titulo" => "Cadastros",
        "icone" => "bi-gear",
        "url" => "#",
        "submenu" => [
            [
                "titulo" => "importação",
                "icone" => "bi-upload",
                "url" => "index.php?page=importacao",
            ],
            [
                "titulo" => "Vendedores",
                "icone" => "bi-people",
                "url" => "index.php?page=cadastro_vendedores",
            ],
            [
                "titulo" => "Aço",
                "icone" => "bi-box-seam",
                "url" => "index.php?page=cadastro_aco",
            ],
            [
                "titulo" => "Equipamentos",
                "icone" => "bi-tools",
                "url" => "index.php?page=cadastro_equipamentos",
            ]
        ]
    ],




    [
        "titulo" => "Relatórios",
        "icone" => "bi-file-earmark-bar-graph",
        "url" => "index.php?page=relatorios"
    ],
    [
        "titulo" => "Temas",
        "icone" => "bi-file-earmark-bar-graph",
        "url" => "index.php?page=temas"
    ],

];
