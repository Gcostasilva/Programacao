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

    campoMotivo.classList.add('d-none');
    campoMotivo.removeAttribute('required');

    const select = document.createElement('select');
    select.id = 'estornoMotivoSelect';
    select.className = 'form-select';
    select.required = true;
    select.innerHTML = '<option value="">Selecione o motivo...</option>';
    campoMotivo.parentNode.insertBefore(select, campoMotivo);

    select.addEventListener('change', function () {
        // O formulário existente envia o campo #estornoMotivo; mantemos esse
        // campo sincronizado para preservar toda a lógica atual do modal.
        campoMotivo.value = select.value;
    });

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