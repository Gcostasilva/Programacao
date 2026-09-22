<div class="modal fade" id="modalAco" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloModalAco">Novo tipo de aço</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="acao" value="salvar">
                    <input type="hidden" name="id" id="aco_id">

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Tipo de aço <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   name="tipo"
                                   id="aco_tipo"
                                   maxlength="50"
                                   required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Ativo</label>
                            <select class="form-select" name="ativo" id="aco_ativo">
                                <option value="1">Sim</option>
                                <option value="0">Não</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
