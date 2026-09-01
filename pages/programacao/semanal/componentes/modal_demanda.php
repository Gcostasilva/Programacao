<div class="modal fade" id="modalBuscaDemanda" tabindex="-1" aria-labelledby="modalBuscaDemandaLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-lg-down modal-xl modal-dialog-centered">
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

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modalBuscaDemanda');

    if (!modal) return;

    modal.addEventListener('show.bs.modal', function (event) {
        const botao = event.relatedTarget;
        window.demandaCampoDestino = botao?.dataset.target || '#codigo';
    });

    modal.addEventListener('hidden.bs.modal', function () {
        window.demandaCampoDestino = '#codigo';
    });
});
</script>