<div class="container-fluid hidden-print">
    <div class="card card-primary card-outline" id="cardFormSemanal">
        <div class="card-header" style="cursor:pointer;">
            <h3 class="card-title">Nova Programação</h3>
            <div class="card-tools"><button type="button" class="btn btn-tool" id="btnToggleFormSemanal" title="Expandir/Retrair"><i class="bi bi-chevron-down" id="iconeToggleFormSemanal"></i></button></div>
        </div>
        <div class="card-body">
            <form action="index.php?page=prog_semanal_novo" method="POST" id="formProgramacaoSemanal" autocomplete="off">
                <div class="row align-items-end">
                    <div class="col-md-2"><label class="form-label">Semana</label><input type="week" class="form-control" id="semana" name="semana" value="<?php echo htmlspecialchars(date('o-\\WW')); ?>" required></div>
                    <div class="col-md-3"><label class="form-label">Recurso</label><select class="form-select" id="recursoSemanal" name="recurso" required>
                            <option value="" disabled selected>Selecione...</option><?php foreach (($dados['recursos_semanal'] ?? []) as $linha): ?><option value="<?= htmlspecialchars($linha['id'], ENT_QUOTES) ?>"><?= htmlspecialchars($linha['descricao']) ?></option><?php endforeach; ?>
                        </select></div>
                    <div class="col-md-2"><label class="form-label">Data</label><input type="date" class="form-control" id="data" name="data" required></div>
                    <div class="col-md-2"><label class="form-label">Demanda</label><input class="form-control" name="demanda" id="demanda" required autocomplete="off"></div>
                    <div></div>
                    <div class="col-md-3"><label class="form-label">Código</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="codigo_s" id="codigo" autocomplete="off" required>
                            <button class="btn btn-primary" type="button" id="btn_buscaCodigo" title="Selecionar código de produto"><i class="bi bi-search"></i></button>
                            <button class="btn btn-primary" type="button" id="btn_buscaDemanda" title="Selecionar demanda"><i class="bi bi-boxes"></i></button>
                        </div>
                    </div>
                    <div class="col-md-5 mt-2"><label class="form-label">Descrição</label><input class="form-control" name="descricao" id="descricao_sem" readonly><input type="hidden" name="complemento_descricao" id="complemento_descricao"></div>
                    <div class="col-md-2 mt-2"><label class="form-label">Quantidade</label><input class="form-control" name="quantidade" id="quantidade" required inputmode="decimal"></div>
                    <div class="col-md-2 mt-2"><label class="form-label">Peso</label><input class="form-control" name="peso" id="peso" inputmode="decimal"></div>
                    <div class="col-md-10 mt-2"><label class="form-label">Observação</label><input class="form-control" name="observacao" id="observacao" autocomplete="off"></div>
                    <div class="col-md-2 mt-2 d-flex gap-2"><button class="btn btn-primary flex-fill" type="submit"><i class="bi bi-save"></i> Salvar</button><button class="btn btn-secondary" type="reset"><i class="bi bi-x-circle"></i></button></div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCodigosProgramacao" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-search me-2"></i>Selecionar produto</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3"><span class="input-group-text"><i class="bi bi-filter"></i></span><input type="text" class="form-control" id="filtroCodigosProgramacao" placeholder="Filtrar por código ou descrição..." autocomplete="off"></div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle" id="tabelaCodigosProgramacao">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Descrição</th>
                                <th>Peso líquido</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (($dados['listaCodigos'] ?? []) as $produto): ?>
                                <tr data-busca="<?= htmlspecialchars(strtolower(($produto['codigo'] ?? '') . ' ' . ($produto['descricao'] ?? '')), ENT_QUOTES) ?>">
                                    <td><?= htmlspecialchars($produto['codigo'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($produto['descricao'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($produto['peso_liquido'] ?? '') ?></td>
                                    <td><button type="button" class="btn btn-sm btn-primary btn-selecionar-codigo" data-codigo="<?= htmlspecialchars($produto['codigo'] ?? '', ENT_QUOTES) ?>" data-descricao="<?= htmlspecialchars($produto['descricao'] ?? '', ENT_QUOTES) ?>" data-peso="<?= htmlspecialchars($produto['peso_liquido'] ?? '', ENT_QUOTES) ?>"><i class="bi bi-check-lg"></i></button></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
