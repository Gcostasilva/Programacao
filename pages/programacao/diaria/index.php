
<div class="container-fluid">

    <div class="row mt-3">
        <div class="col-12 d-flex justify-content-end mb-2 no-print">
            <button type="button" class="btn btn-primary" id="btnImprimirProgramacaoDiaria">
                <i class="bi bi-printer"></i> Imprimir programação
            </button>
        </div>
        <div class="col-12">
            <?php include 'pages\programacao\diaria\componentes\formulario.php'; ?>
        </div>
    </div>

    <div class="row mt-3">
        <?php include 'pages/programacao/diaria/componentes/modal.php'; ?>
        <?php include 'pages/programacao/diaria/componentes/modal_baixa.php'; ?>
        <div class="col-12">
            <?php include 'pages/programacao/diaria/componentes/tabela.php'; ?>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const botao = document.getElementById('btnImprimirProgramacaoDiaria');
    if (!botao) return;

    botao.addEventListener('click', function () {
        const data = document.getElementById('data')?.value || '';
        const recurso = document.getElementById('recurso')?.value || '';

        if (!data || !recurso) {
            alert('Selecione o equipamento e a data antes de imprimir.');
            return;
        }

        // A área de impressão diária utiliza exatamente os dados já carregados
        // na tela. O layout definitivo (cabeçalho/rodapé por equipamento)
        // será aplicado junto ao refinamento do relatório.
        window.print();
    });
});
</script>
