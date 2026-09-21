<div class="container-fluid hidden-print">

    <div class="card card-primary card-outline" id="cardFormSemanal">

        <div class="card-header" style="cursor: pointer;">
            <h3 class="card-title">Pedidos na Industria</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" id="btnToggleFormSemanal" title="Expandir/Retrair">
                    <i class="bi bi-chevron-down" id="iconeToggleFormSemanal"></i>
                </button>
            </div>
        </div>
        <div class="card-body" style="display: inline; grid-template-columns: 1fr 1fr;">
            <form action="index.php?page=pedidos_salvar" method="POST" style="display:flex;" id="formPedido">
                <div id="formSemanal" class="form-pedido col" style="width:90%;">
                    <div class="row">
                        <div class="col-md-2">
                            <label class="form-label">Pedido</label>
                            <div class="input-group">
                                <input class="form-control" id="pedido" name="pedido" required>
                                <button class="btn btn-primary bt-especial" type="button" id="btn_informacao"><i class="bi bi-exclamation"></i></button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Tipo</label>
                            <select class="form-select" name="tipo_pedido" placeholder="Selecione..." required>
                                <option value="" disabled selected>Selecione...</option>
                                <option value="E">Encomenda</option>
                                <option value="P">Padrão</option>
                                <option value="T">Telha</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Semana</label>
                            <input type="date" class="form-control" id="data" name="data" required>
                        </div>
                    </div>

                </div>
                <div style="grid-column: 2/2;flex-direction: column;display: flex;margin-right: 50px;">
                    <button class="btn btn-primary mt-2" type="submit"><i class="bi bi-save"></i> Salvar </button>
                    <button class="btn btn-primary mt-2" type="reset"><i class="bi bi-x-circle"></i> Desistir </button>
                    <button class="btn btn-primary mt-2" type="reset"><i class="bi bi-x-circle"></i> Eliminar Saída </button>
                    <button class="btn btn-primary mt-2" type="reset"><i class="bi bi-x-circle"></i> </button>
                </div>

            </form>
        </div>
        <table class="table table-sm table-hover">
            <thead>
                <tr>
                    <td>Tipo</td>
                    <td>Entrada</td>
                    <td>Usu Entrada</td>
                    <td>Previsão</td>
                    <td>Saída</td>
                    <td>Usu Saída</td>
                </tr>
            </thead>
            <tbody id="tabelaDados">
                <!-- INICIO_LINHAS -->
                
                    <tr>
                        <td>tipo</td>
                        <td>entrada</td>
                        <td>user_entrada</td>
                        <td>previsao'</td>
                        <td>'saida'</td>
                        <td>'user_saida'</td>
                    </tr>
 
                <!-- FIM_LINHAS -->
            </tbody>
        </table>
    </div>
</div>