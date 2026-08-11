<div class="container-fluid">

    <div class="card card-primary card-outline" id="cardFormQuinzena">

        <div class="card-header" style="cursor: pointer;">
            <h3 class="card-title">
                Nova Programação
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" id="btnToggleFormQuinzena" title="Expandir/Retrair">
                    <i class="bi bi-chevron-down" id="iconeToggleFormQuinzena"></i>
                </button>
            </div>
        </div>
        <div class="card-body" style="display: inline; grid-template-columns: 1fr 1fr;">
            <form action="index.php?page=prog_diaria_salvar" method="POST" style="display:flex;">
                <div id="formDiaria" class="form-programacao" style="width:90%;">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Quinzena</label>
                            <select class="form-select">
                                <option>Selecione...</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Código</label>
                            <input class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Descrição</label>
                            <input class="form-control" style="z-index:-100;">
                        </div>
                        <div></div>
                        <div class="col-md-1">
                            <label class="form-label">Quantidade</label>
                            <input class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Ordem de Produção</label>
                            <input class="form-control">
                        </div>
                        <div class="col-md-10">
                            <label class="form-label">Observação</label>
                            <input class="form-control">
                        </div>
                    </div>
                </div>
                <div style="grid-column: 2/2;flex-direction: column;display: flex;margin-right:  50px;">
                    <button class="btn btn-primary mt-2" type="submit">Salvar</button>
                    <button class="btn btn-primary mt-2" type="reset">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>