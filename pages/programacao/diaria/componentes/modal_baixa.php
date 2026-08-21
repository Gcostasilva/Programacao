<div class="modal fade" id="modalBaixar" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Baixar Programação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formBaixar">
                <div class="modal-body">

                    <input type="hidden" id="baixa_id" name="id">

                    <div class="row">
                        <div class="col-md-4 position-relative">
                            <label class="form-label">Pedido</label>
                            <input class="form-control" id="baixa_pedido" name="pedido" autocomplete="off" required>

                            <!-- Lista de resultados da busca (aparece embaixo do campo Pedido) -->
                            <div id="baixa_resultados" class="list-group position-absolute shadow-sm" style="z-index: 1055; width: 100%;"></div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Peso Real</label>
                            <input class="form-control" id="baixa_peso_real" name="peso_real" disabled required>
                        </div>
                    </div>

                    <div class="border border-secondary rounded row mx-sm-0 mt-3 mb-3 pt-3">
                        <div class="col-md-5 mb-2">
                            <label class="form-label">Recurso</label>
                            <select class="form-select" id="baixa_recurso" name="recurso" disabled>
                                <?php foreach ($dados['recursos_diario'] as $linha): ?>
                                    <option value="<?= htmlspecialchars($linha['id']) ?>">
                                        <?= htmlspecialchars($linha['descricao']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Data</label>
                            <input type="date" id="baixa_data" class="form-control" name="data" disabled>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Espessura</label>
                            <input class="form-control" id="baixa_espessura" name="espessura" disabled>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Aço</label>
                            <select class="form-select" id="baixa_aco" name="aco" disabled>
                                <option>AZ-150</option>
                                <option>AZ-120</option>
                                <option>ZAR-280</option>
                                <option>Pré-Pintada</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Vendedor</label>
                            <select class="form-select" id="baixa_vendedor" name="vendedor" disabled>
                                <?php foreach ($dados['vendedores'] as $linha): ?>
                                    <option value="<?= htmlspecialchars($linha['id']) ?>">
                                        <?= htmlspecialchars($linha['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Peso</label>
                            <input class="form-control" id="baixa_peso" name="peso" disabled>
                        </div>

                        <div class="col-md-10 mb-2">
                            <label class="form-label">Observação</label>
                            <input class="form-control" id="baixa_obs" name="observacao" disabled>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="baixa_btn_atualizar" disabled>Atualizar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>