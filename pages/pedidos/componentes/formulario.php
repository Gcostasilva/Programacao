<?php
require_once __DIR__ . '/../../../models/CadastroVendedorModel.php';
require_once __DIR__ . '/../../../models/CadastroMotivoEstornoModel.php';
$vendedoresEstorno = (new CadastroVendedorModel())->listar();
$motivosEstorno = (new CadastroMotivoEstornoModel())->listarAtivos();
?>
<div class="container-fluid hidden-print">
    <div class="card card-primary card-outline" id="cardFormSemanal">
        <div class="card-header" style="cursor: pointer;"><h3 class="card-title">Pedidos na Industria</h3><div class="card-tools"><button type="button" class="btn btn-tool" id="btnToggleFormSemanal" title="Expandir/Retrair"><i class="bi bi-chevron-down" id="iconeToggleFormSemanal"></i></button></div></div>
        <div class="card-body">
            <div class="row g-4 align-items-start">
                <div class="col-12 col-xl-5">
                    <form action="index.php?page=pedidos_salvar" method="POST" id="formPedido">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4"><label class="form-label">Pedido</label><div class="input-group"><input class="form-control" id="pedido" name="pedido" required autocomplete="off"><button class="btn btn-primary bt-especial" type="button" id="btn_estorno" title="Estorno"><i class="bi bi-arrow-counterclockwise"></i></button></div></div>
                            <div class="col-md-4"><label class="form-label">Tipo</label><select class="form-select" name="tipo_pedido" required><option value="" disabled selected>Selecione...</option><option value="E">Encomenda</option><option value="P">Padrão</option><option value="T">Telha</option></select></div>
                            <div class="col-md-4"><label class="form-label">Previsão</label><input type="date" class="form-control" id="data" name="data" required></div>
                        </div>
                        <div class="row mt-3"><div class="col-12 d-flex flex-wrap gap-2"><button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Salvar</button><button class="btn btn-primary" type="reset"><i class="bi bi-x-circle"></i> Desistir</button><button class="btn btn-primary" type="reset"><i class="bi bi-x-circle"></i> Eliminar Saída</button><button class="btn btn-primary" type="reset"><i class="bi bi-x-circle"></i></button></div><div id="pedidoConsultaStatus" class="form-text"></div></div>
                    </form>
                </div>
                <div class="col-12 col-xl-7"><div class="table-responsive"><table class="table table-sm table-hover align-middle mb-0" id="tabelaPedidosIndustria"><thead><tr><th class="text-center" style="width:75px">Ação</th><th>Tipo</th><th>Entrada</th><th>Previsão</th><th>Saída</th></tr></thead><tbody id="tabelaDados"><tr id="pedidoSemDados"><td colspan="5" class="text-center text-muted py-3">Digite um pedido e pressione Tab para consultar as entradas.</td></tr></tbody></table></div></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalComentarioPedido" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title"><i class="bi bi-chat-left-text me-2"></i>Comentário do pedido</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button></div><div class="modal-body"><div class="small text-muted mb-2" id="comentarioPedidoInfo"></div><div id="listaComentariosPedido" class="mb-3"></div><label for="textoComentarioPedido" class="form-label">Novo comentário</label><textarea class="form-control" id="textoComentarioPedido" rows="4" maxlength="2000" placeholder="Digite o comentário..."></textarea></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="button" class="btn btn-primary" id="btnSalvarComentarioPedido"><i class="bi bi-save me-1"></i> Salvar comentário</button></div></div></div></div>

<div class="modal fade" id="modalVisualizarEstornos" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content"><div class="modal-header"><h5 class="modal-title"><i class="bi bi-clock-history me-2"></i>Estornos do pedido</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button></div><div class="modal-body"><div class="d-flex justify-content-between align-items-center mb-3"><div class="text-muted small">Histórico do pedido <strong id="visualizarEstornosPedido">-</strong></div><span class="badge text-bg-danger" id="visualizarEstornosQuantidade">0 estornos</span></div><div id="listaEstornosPedido"><div class="text-muted text-center py-4">Carregando estornos...</div></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button></div></div></div></div>

<div class="modal fade" id="modalEstornoPedido" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><form id="formEstornoPedido"><div class="modal-header"><h5 class="modal-title"><i class="bi bi-arrow-counterclockwise me-2"></i>Estorno do pedido</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button></div><div class="modal-body">
    <div class="alert alert-warning py-2"><i class="bi bi-exclamation-triangle me-1"></i>Pedido: <strong id="estornoPedidoExibicao">-</strong></div>
    <label class="form-label">Tipo de estorno</label><div class="row g-2 mb-3"><div class="col-6"><input class="btn-check" type="radio" name="estorno_total_parcial" id="estornoParcial" value="parcial" autocomplete="off"><label class="btn btn-outline-primary w-100" for="estornoParcial"><i class="bi bi-dash-circle me-1"></i>Estorno parcial</label></div><div class="col-6"><input class="btn-check" type="radio" name="estorno_total_parcial" id="estornoTotal" value="total" autocomplete="off"><label class="btn btn-outline-primary w-100" for="estornoTotal"><i class="bi bi-check2-circle me-1"></i>Estorno total</label></div></div>
    <div class="mb-3"><label for="estornoAtendimento" class="form-label">Atendimento</label><input type="text" class="form-control" id="estornoAtendimento" maxlength="6" required autocomplete="off"></div>
    <div class="mb-3"><label for="estornoVendedor" class="form-label">Vendedor</label><select class="form-select" id="estornoVendedor" required><option value="">Selecione o vendedor...</option><?php foreach ($vendedoresEstorno as $v): ?><?php if ((int)$v['ativo'] === 1): ?><option value="<?= (int)$v['id'] ?>"><?= htmlspecialchars($v['nome'], ENT_QUOTES, 'UTF-8') ?></option><?php endif; ?><?php endforeach; ?></select></div>
    <div class="mb-3"><label for="estornoMotivo" class="form-label">Motivo</label><select class="form-select" id="estornoMotivo" required><option value="">Selecione o motivo...</option><?php foreach ($motivosEstorno as $m): ?><option value="<?= (int)$m['id'] ?>"><?= htmlspecialchars($m['motivo'], ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select></div>
    <div id="estornoErro" class="alert alert-danger mt-3 mb-0 d-none"></div>
</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-danger" id="btnConfirmarEstorno"><i class="bi bi-check-lg me-1"></i>Confirmar estorno</button></div></form></div></div></div>

<!-- Os scripts existentes da página permanecem abaixo desta estrutura. -->
