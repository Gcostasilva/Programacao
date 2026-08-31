<div class="container-fluid hidden-print">

    <div class="card card-primary card-outline" id="cardFormSemanal">

        <div class="card-header" style="cursor: pointer;">
            <h3 class="card-title">
                Nova Programação
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" id="btnToggleFormSemanal" title="Expandir/Retrair">
                    <i class="bi bi-chevron-down" id="iconeToggleFormSemanal"></i>
                </button>
            </div>
        </div>
        <div class="card-body" style="display: inline; grid-template-columns: 1fr 1fr;">
            <form action="index.php?page=prog_semanal_novo" method="POST" style="display:flex;">
                <div id="formSemanal" class="form-programacao" style="width:90%;">
                    <div class="row">
                        <div class="col-md-2">
                            <label class="form-label">Semana</label>
                            <input type="week" class="form-control" id="semana" name="semana" value="<?php $semanaAtual = date('o-\WW');
                            echo $semanaAtual; ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Recurso</label>
                            <select class="form-select" name="recurso" required>
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
                        <div class="col-md-2">
                            <label class="form-label">Data</label>
                            <input type="date" class="form-control" id="data" name="data" required>
                        </div>
                        <div class="col-lg-2">
                            <label class="form-label">Demanda</label>
                            <input class="form-control" name="demanda" id="demanda" required>
                        </div>

                        <div></div>
                        <div class="col-md-3">
                            <label class="form-label">Código</label>
                            <div class="input-group">
                                <input class="form-control" name="codigo_s" id="codigo" required>
                                <input type="hidden" id="espessura_prod">
                                <button class="btn btn-primary bt-especial" type="button" id="btn_buscaCodigo"><i class="bi bi-search"></i></button>
                                <button class="btn btn-primary ms-1 bt-especial" type="button" id="btn_buscaDemanda"><i class="bi bi-boxes"></i></button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Descrição</label>
                            <input class="form-control" name="descricao" id="descricao_sem" disabled>
                            <input type="hidden" class="form-control" name="complemento_descricao"
                                id="complemento_descricao">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">Quantidade</label>
                            <input class="form-control" name="quantidade" id="quantidade" required>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">Peso</label>
                            <input class="form-control" name="peso" id="peso">
                            <input type="hidden" class="form-control" name="peso_liquido" id="peso_liquido" disabled>
                        </div>
                        <div class="col-md-10">
                            <label class="form-label">Observação</label>
                            <input class="form-control" name="observacao" id="observacao">
                        </div>
                    </div>
                </div>
                <div style="grid-column: 2/2;flex-direction: column;display: flex;margin-right:  50px;">
                    <button class="btn btn-primary mt-2" type="submit"><i class="bi bi-save"></i> Salvar </button>
                    <button class="btn btn-primary mt-2" type="reset"><i class="bi bi-x-circle"></i> Desistir </button>
                </div>
            </form>
        </div>
    </div>
</div>