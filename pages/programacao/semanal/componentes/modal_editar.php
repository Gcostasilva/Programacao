<div class="modal fade" id="modalEditarSemanal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar/Baixar Programação <?  ?> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarSemanal">
                <div class="modal-body ">
                    <input type="hidden" name="id" id="edit_sem_id">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Semana</label>
                            <input type="week" class="form-control" id="semana" name="semana"
                                value="<?php $semanaAtual = date('o-\WW');
                                echo $semanaAtual; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Recurso</label>
                            <select class="form-select" name="recurso" id="edit_sem_recurso" required>
                                <option value="" disabled selected>Selecione...</option>
                                <?php
                                foreach ($dados['recursos_semanal'] as $linha) {
                                    $id = htmlspecialchars($linha['id']);
                                    $nome = htmlspecialchars($linha['descricao']);
                                    echo "<option value=\"$id\">$nome</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Data</label>
                            <input type="date" class="form-control" id="edit_sem_data" name="data" required>
                        </div>
                        <div class="col-lg-2">
                            <label class="form-label">Demanda</label>
                            <input class="form-control" name="demanda" id="edit_sem_demanda" required>
                        </div>

                        <div></div>
                        <div class="col-lg-3">
                            <label class="form-label">Código</label>
                            <input class="form-control" name="codigo_s" id="edit_sem_codigo_s" required>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label">Descrição</label>
                            <input class="form-control" name="descricao" id="edit_sem_descricao" disabled>
                            <input type="hidden" class="form-control" name="descricao_COMP" id="edit_descricao_COMP">
                        </div>
                        <div></div>
                        <div class="col-md-2">
                            <label class="form-label">Quantidade</label>
                            <input class="form-control" name="quantidade" id="edit_sem_quantidade" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Peso</label>
                            <input class="form-control" name="peso" id="edit_sem_peso" required>
                            <input type="hidden" class="form-control" name="peso_liquido" id="edit_sem_peso_liquido">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Qtd Real</label>
                            <input class="form-control" name="peca_realizada" id="edit_sem_quantidade_realizada" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Peso Real</label>
                            <input class="form-control" name="peso_realizado" id="edit_sem_peso_realizado" required>
                        </div>
                        <div></div>
                        <div class="col-md-12">
                            <label class="form-label">Observação</label>
                            <input class="form-control" name="observacao" id="edit_sem_observacao">
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