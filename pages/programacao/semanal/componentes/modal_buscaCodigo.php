<div class="modal fade" id="modalBuscaCodigo" tabindex="-1" aria-labelledby="modalBuscaCodigoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBuscaCodigoLabel">Buscar Código do Produto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label for="buscaCodigoDescricao" class="form-label">Pesquisar pela descrição</label>
                    <input type="text" class="form-control" id="buscaCodigoDescricao" placeholder="Digite para filtrar..." autocomplete="off">
                </div>

                <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Código</th>
                                <th>Descrição</th>
                                <th>Peso líquido</th>
                                <th class="text-end">Selecionar</th>
                            </tr>
                        </thead>
                        <tbody id="tabelaBuscaCodigos">
                            <?php foreach ($dados['listaCodigos'] as $codigo): ?>
                                <tr>
                                    <td><?= htmlspecialchars($codigo['codigo']) ?></td>
                                    <td><?= htmlspecialchars($codigo['descricao']) ?></td>
                                    <td><?= htmlspecialchars($codigo['peso_liquido']) ?></td>
                                    <td class="text-end">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-primary btnSelecionarCodigo"
                                            data-codigo="<?= htmlspecialchars($codigo['codigo'], ENT_QUOTES) ?>">
                                            <i class="bi bi-check-lg"></i> Selecionar
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

