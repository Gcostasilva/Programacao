
<div class="container-fluid">

    <div class="row mt-3">

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

        const params = new URLSearchParams({
            page: 'relatorio_programacao_diaria',
            data: data,
            recurso_id: recurso,
            origem: 'diaria'
        });

        window.open('index.php?' + params.toString(), '_blank');
    });
});
</script>
