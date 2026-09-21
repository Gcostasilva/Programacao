    // ======================================================
    // LIMPEZA SEGURA DO BACKDROP DO MODAL DE CHAPA
    // ======================================================
    // O modal de busca de código dispara um blur que pode abrir o modal de
    // chapa durante a transição de fechamento. Em algumas situações o
    // Bootstrap termina com um .modal-backdrop residual. Fazemos a limpeza
    // somente quando realmente não existe outro modal aberto.
    const modalChapa = document.getElementById('modalCalculo_chapa');

    if (modalChapa) {
        modalChapa.addEventListener('hidden.bs.modal', function () {
            setTimeout(function () {
                if (!document.querySelector('.modal.show')) {
                    document.querySelectorAll('.modal-backdrop').forEach(function (backdrop) {
                        backdrop.remove();
                    });
                    document.body.classList.remove('modal-open');
                    document.body.style.removeProperty('padding-right');
                    document.body.style.removeProperty('overflow');
                }
            }, 50);
        });
    }

    // ======================================================
    // SELEÇÃO DE DEMANDA - PROGRAMAÇÃO QUINZENAL
    // ======================================================
    document.addEventListener('DOMContentLoaded', function () {
        const botao = document.getElementById('btn_buscaDemandaQuinzena');
        const modalElement = document.getElementById('modalBuscaDemandaQuinzena');
        const corpo = document.getElementById('modalBuscaDemandaQuinzenaBody');

        if (!botao || !modalElement || !corpo || typeof bootstrap === 'undefined') return;

        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        let carregado = false;
        let carregando = false;

        botao.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            window.demandaCampoDestino = '#produto_id';
            modal.show();
        });

        modalElement.addEventListener('show.bs.modal', function () {
            if (carregado || carregando) return;

            carregando = true;
            corpo.innerHTML = '<div class="text-center py-5"><div class="spinner-border" role="status"></div><div class="mt-2">Carregando demanda...</div></div>';

            fetch('pages/demanda/modal.php', {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(function (resposta) {
                    if (!resposta.ok) throw new Error('HTTP ' + resposta.status);
                    return resposta.text();
                })
                .then(function (html) {
                    const temporario = document.createElement('div');
                    temporario.innerHTML = html;

                    const scripts = Array.from(temporario.querySelectorAll('script'));
                    scripts.forEach(function (script) { script.remove(); });
                    corpo.innerHTML = temporario.innerHTML;

                    scripts.forEach(function (script) {
                        const novoScript = document.createElement('script');
                        if (script.src) novoScript.src = script.src;
                        else novoScript.textContent = script.textContent;
                        document.body.appendChild(novoScript);
                    });

                    carregado = true;
                    carregando = false;
                })
                .catch(function (erro) {
                    carregando = false;
                    corpo.innerHTML = '<div class="alert alert-danger m-3">Não foi possível carregar a Demanda.</div>';
                    console.error('Erro ao carregar Demanda no modal da quinzena:', erro);
                });
        });
    });
