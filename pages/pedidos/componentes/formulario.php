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

        <div class="card-body row" style="display: inline; grid-template-columns: 1fr 1fr;">
            <form action="index.php?page=pedidos_salvar" method="POST" style="display:flex;" id="formPedido">
                <div id="formSemanal" class="form-pedido col" style="width:90%;">
                    <div class="row">
                        <div class="col-md-2">
                            <label class="form-label">Pedido</label>
                            <div class="input-group">
                                <input class="form-control" id="pedido" name="pedido" required autocomplete="off">
                                <button class="btn btn-primary bt-especial" type="button" id="btn_informacao" title="Informações">
                                    <i class="bi bi-exclamation"></i>
                                </button>
                            </div>
                            <div id="pedidoConsultaStatus" class="form-text"></div>
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

                <div class="col-md-5" style="grid-column: 2/2;flex-direction: row;display: flex;margin-right: 50px;">
                    <button class="btn btn-primary me-2" type="submit"><i class="bi bi-save"></i> Salvar </button>
                    <button class="btn btn-primary me-2" type="reset"><i class="bi bi-x-circle"></i> Desistir </button>
                    <button class="btn btn-primary me-2" type="reset"><i class="bi bi-x-circle"></i> Eliminar Saída </button>
                    <button class="btn btn-primary me-2" type="reset"><i class="bi bi-x-circle"></i></button>
                </div>
            </form>
        </div>

        <div class="table-responsive table-responsive-sm col-md-8">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Entrada</th>
                        <th>Previsão</th>
                        <th>Saída</th>
                        <th class="text-center" style="width:90px">Ação</th>
                    </tr>
                </thead>
                <tbody id="tabelaDados">
                    <tr id="pedidoSemDados" >
                        <td colspan="7" class="text-center text-muted py-3">Digite um pedido e pressione Tab para consultar as entradas.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de comentário -->
<div class="modal fade" id="modalComentarioPedido" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-chat-left-text me-2"></i>Comentário do pedido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="small text-muted mb-2" id="comentarioPedidoInfo"></div>
                <div id="listaComentariosPedido" class="mb-3"></div>
                <label for="textoComentarioPedido" class="form-label">Novo comentário</label>
                <textarea class="form-control" id="textoComentarioPedido" rows="4" maxlength="2000" placeholder="Digite o comentário..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSalvarComentarioPedido">
                    <i class="bi bi-save me-1"></i> Salvar comentário
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const campoPedido = document.getElementById('pedido');
    const tabela = document.getElementById('tabelaDados');
    const status = document.getElementById('pedidoConsultaStatus');
    const modalEl = document.getElementById('modalComentarioPedido');
    const modal = new bootstrap.Modal(modalEl);
    const listaComentarios = document.getElementById('listaComentariosPedido');
    const textoComentario = document.getElementById('textoComentarioPedido');
    const infoComentario = document.getElementById('comentarioPedidoInfo');
    const btnSalvarComentario = document.getElementById('btnSalvarComentarioPedido');
    let pedidoIdComentario = null;

    function escapeHtml(valor) {
        const div = document.createElement('div');
        div.textContent = valor ?? '';
        return div.innerHTML;
    }

    function renderizarEntradas(registros) {
        tabela.innerHTML = '';

        if (!registros.length) {
            tabela.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-3">Nenhuma entrada encontrada para este pedido.</td></tr>';
            return;
        }

        registros.forEach(registro => {
            const tr = document.createElement('tr');
            const comentarios = Number(registro.total_comentarios || 0);
            const saida = registro.saida ?? ""
            tr.innerHTML = `
                <td>${escapeHtml(registro.tipo)}</td>
                <td>${escapeHtml(registro.entrada + " - " + registro.user_entrada)}</td>
                <td>${escapeHtml(registro.previsao)}</td>
                <td>${escapeHtml(saida  + " - " + registro.user_saida)}</td>

                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-primary btn-comentario-pedido"
                            data-id="${escapeHtml(registro.id)}"
                            data-pedido="${escapeHtml(registro.pedido)}"
                            title="Adicionar comentário">
                        <i class="bi bi-chat-left-text"></i>
                        ${comentarios ? `<span class="badge text-bg-primary ms-1">${comentarios}</span>` : ''}
                    </button>
                </td>`;
            tabela.appendChild(tr);
        });
    }

    async function consultarPedido() {
        const pedido = campoPedido.value.trim();
        if (!pedido) return;

        status.textContent = 'Consultando entradas...';
        status.className = 'form-text text-primary';

        try {
            const resposta = await fetch('index.php?page=pedidos_buscar&pedido=' + encodeURIComponent(pedido), { cache: 'no-store' });
            if (!resposta.ok) throw new Error('HTTP ' + resposta.status);
            const dados = await resposta.json();
            renderizarEntradas(Array.isArray(dados) ? dados : []);
            status.textContent = `${Array.isArray(dados) ? dados.length : 0} entrada(s) encontrada(s).`;
            status.className = 'form-text text-success';
        } catch (erro) {
            console.error(erro);
            tabela.innerHTML = '<tr><td colspan="7" class="text-center text-danger py-3">Erro ao consultar o pedido.</td></tr>';
            status.textContent = 'Não foi possível realizar a consulta.';
            status.className = 'form-text text-danger';
        }
    }

    // A consulta acontece especificamente ao pressionar TAB no campo Pedido.
    campoPedido.addEventListener('keydown', function (event) {
        if (event.key === 'Tab') {
            consultarPedido();
        }
    });

    tabela.addEventListener('click', async function (event) {
        const botao = event.target.closest('.btn-comentario-pedido');
        if (!botao) return;

        pedidoIdComentario = botao.dataset.id;
        const pedido = botao.dataset.pedido;
        infoComentario.textContent = 'Pedido ' + pedido + ' • Entrada #' + pedidoIdComentario;
        textoComentario.value = '';
        listaComentarios.innerHTML = '<div class="text-muted small">Carregando comentários...</div>';
        modal.show();

        try {
            const resposta = await fetch('index.php?page=pedidos_comentario&acao=listar&pedido_id=' + encodeURIComponent(pedidoIdComentario), { cache: 'no-store' });
            if (!resposta.ok) throw new Error('HTTP ' + resposta.status);
            const comentarios = await resposta.json();

            if (!comentarios.length) {
                listaComentarios.innerHTML = '<div class="alert alert-light border small mb-0">Nenhum comentário cadastrado para esta entrada.</div>';
                return;
            }

            listaComentarios.innerHTML = comentarios.map(c => `
                <div class="border rounded p-2 mb-2">
                    <div class="small text-muted mb-1">${escapeHtml(c.usuario || 'Usuário')} • ${escapeHtml(c.criado_em)}</div>
                    <div>${escapeHtml(c.comentario).replace(/\n/g, '<br>')}</div>
                </div>`).join('');
        } catch (erro) {
            console.error(erro);
            listaComentarios.innerHTML = '<div class="text-danger small">Não foi possível carregar os comentários.</div>';
        }
    });

    btnSalvarComentario.addEventListener('click', async function () {
        const comentario = textoComentario.value.trim();
        if (!pedidoIdComentario || !comentario) {
            textoComentario.focus();
            return;
        }

        btnSalvarComentario.disabled = true;
        try {
            const dados = new FormData();
            dados.append('pedido_id', pedidoIdComentario);
            dados.append('comentario', comentario);

            const resposta = await fetch('index.php?page=pedidos_comentario', { method: 'POST', body: dados });
            const resultado = await resposta.json();
            if (!resposta.ok || !resultado.sucesso) throw new Error(resultado.erro || 'Falha ao salvar');

            textoComentario.value = '';
            modal.hide();
            await consultarPedido();
        } catch (erro) {
            console.error(erro);
            alert(erro.message || 'Não foi possível salvar o comentário.');
        } finally {
            btnSalvarComentario.disabled = false;
        }
    });
});
</script>