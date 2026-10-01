<div class="container-fluid">

    <div class="row mt-3 ">
        <div class="col-12">            
            <?php include 'componentes/formulario.php'; ?>
        </div>
    </div>

    <div class="row mt-3">

        <div class="col-12">
            <?php include 'componentes/tabela.php'; ?>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', async function () {
    const campoMotivo = document.getElementById('estornoMotivo');
    if (!campoMotivo) return;

    const select = document.createElement('select');
    select.id = 'estornoMotivo';
    select.className = 'form-select';
    select.required = true;
    select.innerHTML = '<option value="">Selecione o motivo...</option>';
    campoMotivo.replaceWith(select);

    try {
        const resp = await fetch('index.php?page=pedidos_motivos_estorno', { cache: 'no-store' });
        if (!resp.ok) throw new Error('HTTP ' + resp.status);
        const motivos = await resp.json();
        motivos.forEach(m => {
            const option = document.createElement('option');
            option.value = m.id;
            option.textContent = m.motivo;
            select.appendChild(option);
        });
    } catch (e) {
        console.error('Não foi possível carregar os motivos de estorno:', e);
    }
});
</script>