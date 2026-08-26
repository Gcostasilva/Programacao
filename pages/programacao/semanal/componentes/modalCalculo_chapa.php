<div class="modal fade" id="modalCalculo_chapa" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Calcular Programação de Chapa ou Bobininha</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCalculo_chapa">
                <div class="row g-3 modal-body">
                    <div class="col-md-2">
                        <label class="form-label">Espessura</label>
                        <input type="number" class="form-control" id="espessura_chapa" name="espessura_chapa"
                            step="0.001" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Comprimento</label>
                        <input type="number" class="form-control" id="comprimento_chapa" name="comprimento_chapa"
                            required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Largura</label>
                        <input type="number" class="form-control" id="largura_chapa" name="largura_chapa" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Peso por Peça</label>
                        <input type="number" class="form-control" id="peso_peca_chapa" name="peso_peca_chapa"
                            step="0.001" required>
                    </div>
                    <div></div>
                    <div class="col-md-3">
                        <label class="form-label">Quantidade</label>
                        <input type="number" class="form-control" id="quantidade_chapa" name="quantidade_chapa"
                            required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Peso</label>
                        <input type="number" class="form-control" id="peso_chapa" name="peso_chapa" step="0.001"
                            required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btnAplicarCalculoChapa">Aplicar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>