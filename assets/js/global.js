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