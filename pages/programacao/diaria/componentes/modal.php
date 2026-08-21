<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Programação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditar">
                <div class="modal-body ">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Recurso</label>
                            <select class="form-select" id="edit_recurso" name="recurso" required>
                                <?php foreach ($dados['recursos_diario'] as $linha): ?>
                                    <option value="<?= htmlspecialchars($linha['id']) ?>">
                                        <?= htmlspecialchars($linha['descricao']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Data</label>
                            <input type="date" id="edit_data" class="form-control" name="data" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Pedido</label>
                            <input class="form-control" id="edit_pedido" name="pedido" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Espessura</label>
                            <input class="form-control" id="edit_espessura" name="espessura" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Aço</label>
                            <select class="form-select" id="edit_aco" name="aco" required>
                                <option>AZ-150</option>
                                <option>AZ-120</option>
                                <option>ZAR-280</option>
                                <option>Pré-Pintada</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Vendedor</label>
                            <select class="form-select" id="edit_vendedor" name="vendedor" required>
                                <?php foreach ($dados['vendedores'] as $linha): ?>
                                    <option value="<?= htmlspecialchars($linha['id']) ?>">
                                        <?= htmlspecialchars($linha['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Peso</label>
                            <input class="form-control" id="edit_peso" name="peso" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Peso Real</label>
                            <input class="form-control" id="edit_peso_real" name="peso_real" autofocus>
                        </div>
                        <div class="col-md-2 text-center align-self-center">
                            <input class="form-check-input" type="checkbox" id="falta_mp_at" name="falta_mp_at">
                            <label class="form-check-label" for="falta_mp_at">Falta MP</label>
                        </div>
                        <div class="col-md-10">
                            <label class="form-label">Observação</label>
                            <input class="form-control" id="edit_obs" name="observacao">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Atualizar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>