<div class="container-fluid">

    <div class="row mt-3">
        <div class="col-12 d-flex justify-content-end mb-2 no-print">

        </div>
        <div class="col-12">
            <?php include 'pages\\programacao\\quinzena\\componentes\\formulario.php'; ?>
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
        const quinzena = document.getElementById('quinzena')?.value || '';

        if (!quinzena) {
            alert('Selecione o mês e a quinzena antes de imprimir.');
            return;
        }

        const params = new URLSearchParams({
            page: 'relatorio_programacao_quinzenal',
            quinzena: quinzena,
            origem: 'quinzenal'
        });

        window.location.href = 'index.php?' + params.toString();
    });
});
</script>
