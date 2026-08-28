
// Função para alternar tema de cores
// (sidebar / navbar / footer / primária)

document.addEventListener('DOMContentLoaded', function () {

    const storageKey = 'temaPreset';

    // ======================================================
    // DEFINIÇÃO DOS TEMAS
    // ======================================================
    const CATEGORIAS = [
        {
            nome: 'Light',
            desc: 'Fundo branco; os widgets e a cor escolhida é que dão o destaque.',
            temas: [
                {
                    id: 'light',
                    label: 'Light',
                    sidebar: '#ffffff',
                    navbar: '#ffffff',
                    footer: '#ffffff',
                    texto: '#111827',
                    modo: 'light',
                    primary: '#0d6efd',
                    chips: ['#0d6efd', '#20c997']
                },
                {
                    id: 'light-indigo',
                    label: 'Light & indigo',
                    sidebar: '#ffffff',
                    navbar: '#ffffff',
                    footer: '#ffffff',
                    texto: '#111827',
                    modo: 'light',
                    primary: '#6610f2',
                    chips: ['#6610f2', '#d63384']
                },
                {
                    id: 'light-sky',
                    label: 'Light & sky',
                    sidebar: '#ffffff',
                    navbar: '#ffffff',
                    footer: '#ffffff',
                    texto: '#111827',
                    modo: 'light',
                    primary: '#0dcaf0',
                    chips: ['#0dcaf0', '#fd7e14']
                }
            ]
        },

        {
            nome: 'Semi-dark',
            desc: 'Sidebar escura, cabeçalho claro — o layout mais comum.',
            temas: [
                {
                    id: 'default',
                    label: 'Default',
                    sidebar: '#343a40',
                    navbar: '#ffffff',
                    footer: '#ffffff',
                    texto: '#111827',
                    modo: 'light',
                    primary: '#0d6efd',
                    chips: ['#20c997', '#0d6efd']
                },
                {
                    id: 'graphite',
                    label: 'Graphite',
                    sidebar: '#4b5563',
                    navbar: '#ffffff',
                    footer: '#ffffff',
                    texto: '#111827',
                    modo: 'light',
                    primary: '#fd7e14',
                    chips: ['#6610f2', '#fd7e14']
                },
                {
                    id: 'navy',
                    label: 'Navy',
                    sidebar: '#1b2a4a',
                    navbar: '#ffffff',
                    footer: '#ffffff',
                    texto: '#111827',
                    modo: 'light',
                    primary: '#0dcaf0',
                    chips: ['#20c997', '#d63384']
                },
                {
                    id: 'steel',
                    label: 'Steel',
                    sidebar: '#64748b',
                    navbar: '#ffffff',
                    footer: '#ffffff',
                    texto: '#111827',
                    modo: 'light',
                    primary: '#d63384',
                    chips: ['#0d6efd', '#fd7e14']
                },
                {
                    id: 'midnight',
                    label: 'Midnight',
                    sidebar: '#0f172a',
                    navbar: '#ffffff',
                    footer: '#ffffff',
                    texto: '#111827',
                    modo: 'light',
                    primary: '#20c997',
                    chips: ['#20c997', '#fd7e14']
                },
                {
                    id: 'indigo',
                    label: 'Indigo',
                    sidebar: '#6610f2',
                    navbar: '#ffffff',
                    footer: '#ffffff',
                    texto: '#111827',
                    modo: 'light',
                    primary: '#6610f2',
                    chips: ['#20c997', '#d63384']
                }
            ]
        },

        {
            nome: 'Full dark',
            desc: 'Sidebar e cabeçalho escuros; o corpo continua claro.',
            temas: [
                {
                    id: 'navy-sky',
                    label: 'Navy & sky',
                    sidebar: '#1b2a4a',
                    navbar: '#1b2a4a',
                    footer: '#ffffff',
                    texto: '#ffffff',
                    modo: 'dark',
                    primary: '#0dcaf0',
                    chips: ['#20c997', '#d63384']
                },
                {
                    id: 'midnight-mono',
                    label: 'Midnight mono',
                    sidebar: '#0f172a',
                    navbar: '#0f172a',
                    footer: '#ffffff',
                    texto: '#ffffff',
                    modo: 'dark',
                    primary: '#6610f2',
                    chips: ['#6610f2', '#d63384']
                },
                {
                    id: 'steel-mono',
                    label: 'Steel mono',
                    sidebar: '#64748b',
                    navbar: '#64748b',
                    footer: '#ffffff',
                    texto: '#ffffff',
                    modo: 'dark',
                    primary: '#20c997',
                    chips: ['#20c997', '#0d6efd']
                },
                {
                    id: 'slate-teal',
                    label: 'Slate & teal',
                    sidebar: '#475569',
                    navbar: '#475569',
                    footer: '#ffffff',
                    texto: '#ffffff',
                    modo: 'dark',
                    primary: '#20c997',
                    chips: ['#20c997', '#0d6efd']
                },
                {
                    id: 'graphite-orange',
                    label: 'Graphite & orange',
                    sidebar: '#4b5563',
                    navbar: '#4b5563',
                    footer: '#ffffff',
                    texto: '#ffffff',
                    modo: 'dark',
                    primary: '#fd7e14',
                    chips: ['#fd7e14', '#6610f2']
                }
            ]
        },

        {
            nome: 'Colorido',
            desc: 'Uma cor forte só, ou o efeito gradiente.',
            temas: [
                {
                    id: 'violeta',
                    label: 'Violeta mono',
                    sidebar: '#6f42c1',
                    navbar: '#6f42c1',
                    footer: '#ffffff',
                    texto: '#ffffff',
                    modo: 'dark',
                    primary: '#6f42c1',
                    chips: ['#fd7e14', '#20c997']
                },
                {
                    id: 'gradiente-indigo',
                    label: 'Gradiente indigo',
                    sidebar: 'linear-gradient(160deg,#6610f2,#9c6bff)',
                    navbar: 'linear-gradient(90deg,#6610f2,#9c6bff)',
                    footer: '#ffffff',
                    texto: '#ffffff',
                    modo: 'dark',
                    primary: '#6610f2',
                    chips: ['#20c997', '#fd7e14']
                },
                {
                    id: 'gradiente-teal',
                    label: 'Gradiente teal',
                    sidebar: 'linear-gradient(160deg,#0f766e,#2dd4bf)',
                    navbar: 'linear-gradient(90deg,#0f766e,#2dd4bf)',
                    footer: '#ffffff',
                    texto: '#ffffff',
                    modo: 'dark',
                    primary: '#20c997',
                    chips: ['#fd7e14', '#0d6efd']
                }
            ]
        }
    ];

    const area = document.getElementById('areaCategorias');

// ======================================================
// MONTA A GRADE DE MINIATURAS
// SOMENTE SE ESTIVER NA PÁGINA DE TEMAS
// ======================================================
if (area) {

    CATEGORIAS.forEach(categoria => {

        const bloco = document.createElement('div');

        bloco.className = 'mb-4';

        bloco.innerHTML = `
            <div class="categoria-titulo">${categoria.nome}</div>
            <div class="categoria-desc">${categoria.desc}</div>
            <div class="row row-cols-3 row-cols-md-6 g-2" data-lista></div>
        `;

        const lista = bloco.querySelector('[data-lista]');

        categoria.temas.forEach(tema => {

            const col = document.createElement('div');

            col.className = 'col';

            col.innerHTML = `
                <div class="preset-card" data-id="${tema.id}">
                    <div class="mini-device">

                        <div
                            class="mini-sidebar"
                            style="background:${tema.sidebar};">
                        </div>

                        <div class="mini-main">

                            <div
                                class="mini-navbar"
                                style="background:${tema.navbar};">

                                <div
                                    class="dot"
                                    style="background:${
                                        tema.modo === 'dark'
                                            ? 'rgba(255,255,255,.6)'
                                            : 'rgba(0,0,0,.35)'
                                    };">
                                </div>

                            </div>

                            <div class="mini-content">

                                <div
                                    class="mini-chip"
                                    style="background:${tema.chips[0]};">
                                </div>

                                <div
                                    class="mini-chip"
                                    style="background:${tema.chips[1]};">
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="preset-label">
                        ${tema.label}
                    </div>
                </div>
            `;

            col.querySelector('.preset-card')
                .addEventListener('click', function () {
                    aplicarTema(tema, true);
                });

            lista.appendChild(col);

        });

        area.appendChild(bloco);

    });

}




    // ======================================================
    // APLICA O TEMA
    // ======================================================
    function aplicarTema(tema, salvar) {

        const sidebar = document.querySelector('.app-sidebar');
        const navbar = document.querySelector('.app-header');
        const footer = document.querySelector('.app-footer');
        const html = document.documentElement;


        // ------------------------------------------
        // SIDEBAR
        // ------------------------------------------
        if (sidebar) {

            sidebar.style.setProperty(
                'background',
                tema.sidebar,
                'important'
            );

            sidebar.setAttribute(
                'data-bs-theme',
                tema.modo
            );
        }


        // ------------------------------------------
        // NAVBAR
        // ------------------------------------------
        if (navbar) {

            navbar.style.setProperty(
                'background',
                tema.navbar,
                'important'
            );

            navbar.setAttribute(
                'data-bs-theme',
                tema.modo
            );
        }


        // ------------------------------------------
        // FOOTER
        // ------------------------------------------
        if (footer) {

            footer.style.setProperty(
                'background',
                tema.footer,
                'important'
            );

            footer.setAttribute(
                'data-bs-theme',
                tema.modo
            );
        }


        // ------------------------------------------
        // COR PRIMÁRIA
        // ------------------------------------------
        html.style.setProperty(
            '--bs-primary',
            tema.primary
        );

        html.style.setProperty(
            '--bs-primary-rgb',
            hexParaRgb(tema.primary)
        );


        // ------------------------------------------
        // CARD ATIVO
        // SOMENTE NA PÁGINA DE TEMAS
        // ------------------------------------------
        if (area) {

            document
                .querySelectorAll('.preset-card')
                .forEach(function (card) {
                    card.classList.remove('ativo');
                });


            const cardAtivo = document.querySelector(
                `.preset-card[data-id="${tema.id}"]`
            );


            if (cardAtivo) {
                cardAtivo.classList.add('ativo');
            }

        }


        // ------------------------------------------
        // SALVA
        // ------------------------------------------
        if (salvar) {

            localStorage.setItem(
                storageKey,
                JSON.stringify(tema)
            );

        }

    }


    // ======================================================
    // CONVERTE HEX PARA RGB
    // ======================================================
    function hexParaRgb(valor) {

        // Gradiente não é uma cor HEX.
        // Nesse caso usamos a cor primária.
        if (!valor || !valor.startsWith('#')) {
            return '13, 110, 253';
        }


        const hex = valor.replace('#', '');

        const bigint = parseInt(hex, 16);


        return `${(bigint >> 16) & 255}, ${(bigint >> 8) & 255}, ${bigint & 255}`;

    }


    // ======================================================
    // RESTAURA O TEMA SALVO
    // EXECUTA EM TODAS AS PÁGINAS
    // ======================================================
    const salvo = localStorage.getItem(storageKey);


    if (salvo) {

        try {

            const tema = JSON.parse(salvo);

            aplicarTema(tema, false);

        } catch (erro) {

            console.error(
                'Erro ao restaurar o tema:',
                erro
            );

            localStorage.removeItem(storageKey);

        }

    }