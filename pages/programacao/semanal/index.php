<div class="container-fluid">

    <div class="row mt-3">
        <div class="col-12 d-flex justify-content-end mb-2 no-print">
            <button type="button" class="btn btn-primary" id="btnImprimirProgramacaoSemanal">
                <i class="bi bi-printer"></i> Imprimir programação
            </button>
        </div>
        <div class="col-12">
            <?php include 'pages\\programacao\\semanal\\componentes\\formulario.php'; ?>
            <?php include 'pages/programacao/semanal/componentes/modal_editar.php'; ?>
            <?php include 'pages/programacao/semanal/componentes/modalCalculo_chapa.php'; ?>
            <?php include 'pages/programacao/semanal/componentes/modalCalculo_perfil.php'; ?>
            <?php include 'pages/programacao/semanal/componentes/modal_buscaCodigo.php'; ?>
            <?php include 'pages/programacao/semanal/componentes/modal_demanda_selecao.php'; ?>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <?php include 'pages/programacao/semanal/componentes/tabela.php'; ?>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const botao = document.getElementById('btnImprimirProgramacaoSemanal');
    if (!botao) return;

    botao.addEventListener('click', function () {
        const semana = document.getElementById('semana_filtro')?.value || document.getElementById('semana')?.value;
        const recurso = document.getElementById('recurso_filtro')?.value || document.getElementById('recursoSemanal')?.value || '';

        if (!semana) {
            alert('Selecione uma semana antes de imprimir.');
            return;
        }

        const params = new URLSearchParams({
            page: 'relatorio_programacao',
            semana: semana,
            recurso_id: recurso,
            origem: 'semanal'
        });

        window.open('index.php?' + params.toString(), '_blank');
    });
});
</script>