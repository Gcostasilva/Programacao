<div class="container-fluid">

    <div class="card card-primary card-outline" id="cardFormDiaria">

        <div class="card-header" style="cursor: pointer;">
            <h3 class="card-title">
                Nova Programação
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" id="btnToggleFormDiaria" title="Expandir/Retrair">
                    <i class="bi bi-chevron-down" id="iconeToggleFormDiaria"></i>
                </button>
            </div>
        </div>
        <div class="card-body" style="display: inline; grid-template-columns: 1fr 1fr;">
            <form action="index.php?page=prog_diaria_salvar" id="formDiario" method="POST" style="display:flex;">
                <div id="formDiaria" class="form-programacao" style="width:110%; margin-left:50px; grid-column: 1/2;">
                    <input type="hidden" name="acao" value="salvar">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Recurso</label>
                            <select class="form-select" id="recurso" name="recurso" placeholder="Selecione..." required>
                                <option value="" disabled selected>Selecione...</option>
                                <?php
                                foreach ($dados['recursos_diario'] as $linha) {
                                    $id = htmlspecialchars($linha['id']);
                                    $nome = htmlspecialchars($linha['descricao']);
                                    echo "<option value=\"$id\">$nome</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Data</label>
                            <input type="date" id="data" class="form-control" name="data" required>
                        </div>
                        <div></div>
                        <div class="col-md-2">
                            <label class="form-label">Pedido</label>
                            <input class="form-control" name="pedido" required>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">Espessura</label>
                            <input class="form-control" name="espessura" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Aço</label>
                            <select class="form-select" name="aco" placeholder="Selecione..." required>
                                <option value="" disabled selected>Selecione...</option>
                                <option>AZ-150</option>
                                <option>AZ-120</option>
                                <option>ZAR-280</option>
                                <option>Pré-Pintada</option>
                            </select>
                        </div>
                        <div class="col-md-3">

                            <label class="form-label">Vendedor</label>
                            <select class="form-select" name="vendedor" required>
                                <option value="" disabled selected>Selecione...</option>
                                <?php
                                foreach ($dados['vendedores'] as $linha) {
                                    $id = htmlspecialchars($linha['id']);
                                    $nome = htmlspecialchars($linha['nome']);
                                    echo "<option value=\"$id\">$nome</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Peso</label>
                            <input class="form-control" name="peso" required>
                        </div>
                        <div></div>
                        <div class="col-md-1 text-center align-self-center">
                            <input class="form-check-input" type="checkbox" id="falta_mp" name="falta_mp">
                            <label class="form-check-label" for="falta_mp">Falta MP</label>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label">Observação</label>
                            <input class="form-control" name="observacao">
                        </div>
                    </div>
                </div>
                <div style="grid-column: 2/2;flex-direction: column;display: flex;margin-right:  20px;">
                    <a data-bs-toggle="modal" data-bs-target="#modalBaixar" class="btn btn-primary mt-2" > <i class="bi bi-download"></i> Baixar</a>
                    <button class="btn btn-primary mt-2" type="submit">  <i class="bi bi-save"></i> Salvar</button>
                    <button class="btn btn-primary mt-2" type="reset"> <i class="bi bi-x-circle"></i> Desistir</button>
                </div>
            </form>
        </div>
    </div>
</div>