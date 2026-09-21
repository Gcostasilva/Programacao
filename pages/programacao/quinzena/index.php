<div class="container-fluid">

    <div class="row mt-3">
        <div class="col-12 d-flex justify-content-end mb-2 no-print">
            <button type="button" class="btn btn-primary" id="btnImprimirProgramacaoQuinzena">
                <i class="bi bi-printer"></i> Imprimir programação
            </button>
        </div>
        <div class="col-12">
            <?php include 'pages\programacao\quinzena\componentes\formulario.php'; ?>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <?php include 'pages/programacao/quinzena/componentes/tabela.php'; ?>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const botao = document.getElementById('btnImprimirProgramacaoQuinzena');
    if (!botao) return;

    botao.addEventListener('click', function () {
        window.print();
    });
});
</script>
