
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
            nome: 'Dark',
            desc: 'Interface escura completa, com contraste confortável.',
            temas: [
                {
                    id: 'dark',
                    label: 'Dark',
                    sidebar: '#111827',
                    navbar: '#111827',
                    footer: '#111827',
                    texto: '#e5e7eb',
                    modo: 'dark',
                    body: '#0f172a',
                    surface: '#1e293b',
                    border: '#334155',
                    primary: '#0d6efd',
                    chips: ['#0d6efd', '#20c997']
                },
                {
                    id: 'dark-teal',
                    label: 'Dark & teal',
                    sidebar: '#0f172a',
                    navbar: '#0f172a',
                    footer: '#0f172a',
                    texto: '#e2e8f0',
                    modo: 'dark',
                    body: '#0b1414',
                    surface: '#132525',
                    border: '#254545',
                    primary: '#20c997',
                    chips: ['#20c997', '#0d6efd']
                },
                {
                    id: 'dark-indigo',
                    label: 'Dark & indigo',
                    sidebar: '#111827',
                    navbar: '#111827',
                    footer: '#111827',
                    texto: '#e5e7eb',
                    modo: 'dark',
                    body: '#11101d',
                    surface: '#211d35',
                    border: '#393252',
                    primary: '#8b5cf6',
                    chips: ['#8b5cf6', '#d63384']
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
                    texto: '#1a1717',
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
                    texto: '#241d1d',
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
                    texto: '#241d1d',
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
                    texto: '#241d1d',
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
                                    style="background:${tema.modo === 'dark'
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

        // Identifica o tema atual
        html.setAttribute('data-tema', tema.id);

        // Modo Bootstrap
        //sidebar.setAttribute('data-bs-theme', tema.modo);
        //navbar.setAttribute('data-bs-theme', tema.modo);
        //footer.setAttribute('data-bs-theme', tema.modo);

        // Sidebar
        if (sidebar) {
            html.style.setProperty(
                '--tema-sidebar',
                tema.sidebar
            );

            sidebar.setAttribute(
                'data-bs-theme',
                tema.modo
            );
        }

        // Navbar
        if (navbar) {
            html.style.setProperty(
                '--tema-navbar',
                tema.navbar
            );

            navbar.setAttribute(
                'data-bs-theme',
                tema.modo
            );
        }

        if (footer) {
            html.style.setProperty(
                '--tema-footer',
                tema.footer
            );

            footer.setAttribute(
                'data-bs-theme',
                tema.modo
            );
        }

        // ==================================================
        // CORES GLOBAIS BOOTSTRAP
        // ==================================================

        const rgb = hexParaRgb(tema.primary);

        html.style.setProperty('--tema-primary', tema.primary);
        html.style.setProperty('--tema-primary-rgb', rgb);
        html.style.setProperty('--tema-body', tema.body || '#ffffff');
        html.style.setProperty('--tema-surface', tema.surface || '#ffffff');
        html.style.setProperty('--tema-border', tema.border || '#dee2e6');
        html.style.setProperty('--tema-texto', tema.texto);

        html.style.setProperty(
            '--tema-focus',
            `rgba(${rgb}, .25)`
        );

        // ==================================================
        // CARD ATIVO
        // ==================================================

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

        // ==================================================
        // SALVA
        // ==================================================

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
});