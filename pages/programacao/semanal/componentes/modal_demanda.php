<div class="modal fade" id="modalBuscaDemanda" tabindex="-1" aria-labelledby="modalBuscaDemandaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBuscaDemandaLabel">Selecionar Demanda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <?php
                $modoDemanda = 'selecao';
                $idTabelaDemanda = 'tb_demanda_modal';
                include 'pages/demanda/componente/tabela.php';
                ?>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    function configurarModalDemanda() {
        const modal = document.getElementById('modalBuscaDemanda');
        if (!modal || modal.dataset.configurado === '1') return;

        modal.dataset.configurado = '1';
        modal.addEventListener('show.bs.modal', function (event) {
            const botao = event.relatedTarget;
            window.demandaCampoDestino = botao?.dataset.target || '#codigo';
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', configurarModalDemanda);
    } else {
        configurarModalDemanda();
    }
})();
</script>