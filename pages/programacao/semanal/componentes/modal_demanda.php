<div class="modal fade" id="modalBuscaDemanda" tabindex="-1" aria-labelledby="modalBuscaDemandaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBuscaDemandaLabel">
                    <i class="bi bi-boxes"></i> Selecionar Demanda
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body p-2" id="modalBuscaDemandaBody">
                <div class="text-center py-5" id="modalBuscaDemandaLoading">
                    <div class="spinner-border" role="status"></div>
                    <div class="mt-2">Carregando demanda...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const botao = document.getElementById('btn_buscaDemanda');
    const modalElement = document.getElementById('modalBuscaDemanda');
    const corpo = document.getElementById('modalBuscaDemandaBody');

    if (!botao || !modalElement || !corpo) return;

    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    let carregado = false;
    let carregando = false;

    botao.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();

        window.demandaCampoDestino = '#codigo';
        modal.show();
    });

    modalElement.addEventListener('show.bs.modal', function () {
        if (carregado || carregando) return;

        carregando = true;
        corpo.innerHTML = '<div class="text-center py-5"><div class="spinner-border" role="status"></div><div class="mt-2">Carregando demanda...</div></div>';

        fetch('pages/demanda/index.php?modal=1', {
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
                if (script.src) {
                    novoScript.src = script.src;
                } else {
                    novoScript.textContent = script.textContent;
                }
                document.body.appendChild(novoScript);
            });

            carregado = true;
            carregando = false;
        })
        .catch(function (erro) {
            carregando = false;
            corpo.innerHTML = '<div class="alert alert-danger m-3">Não foi possível carregar a Demanda.</div>';
            console.error('Erro ao carregar Demanda no modal:', erro);
        });
    });

    modalElement.addEventListener('hidden.bs.modal', function () {
        window.demandaCampoDestino = '#codigo';
    });
})();
</script>