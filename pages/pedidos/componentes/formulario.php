<?php
require_once __DIR__ . '/../../../models/CadastroVendedorModel.php';
$vendedoresEstorno = (new CadastroVendedorModel())->listar();
?>
<div class="container-fluid hidden-print">
    <div class="card card-primary card-outline" id="cardFormSemanal">
        <div class="card-header" style="cursor: pointer;">
            <h3 class="card-title">Pedidos na Industria</h3>
            <div class="card-tools"><button type="button" class="btn btn-tool" id="btnToggleFormSemanal" title="Expandir/Retrair"><i class="bi bi-chevron-down" id="iconeToggleFormSemanal"></i></button></div>
        </div>
        <div class="card-body">
            <div class="row g-4 align-items-start">
                <div class="col-12 col-xl-5">
                    <form action="index.php?page=pedidos_salvar" method="POST" id="formPedido">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4"><label class="form-label">Pedido</label>
                                <div class="input-group"><input class="form-control" id="pedido" name="pedido" required autocomplete="off"><button class="btn btn-primary bt-especial" type="button" id="btn_estorno" title="Estorno"><i class="bi bi-arrow-counterclockwise"></i></button></div>
                                
                            </div>
                            <div class="col-md-4"><label class="form-label">Tipo</label><select class="form-select" name="tipo_pedido" required>
                                    <option value="" disabled selected>Selecione...</option>
                                    <option value="E">Encomenda</option>
                                    <option value="P">Padrão</option>
                                    <option value="T">Telha</option>
                                </select></div>
                            <div class="col-md-4"><label class="form-label">Previsão</label><input type="date" class="form-control" id="data" name="data" required></div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 d-flex flex-wrap gap-2"><button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Salvar</button><button class="btn btn-primary" type="reset"><i class="bi bi-x-circle"></i> Desistir</button><button class="btn btn-primary" type="reset"><i class="bi bi-x-circle"></i> Eliminar Saída</button><button class="btn btn-primary" type="reset"><i class="bi bi-x-circle"></i></button></div>
                            <div id="pedidoConsultaStatus" class="form-text"></div>
                        </div>
                    </form>
                </div>
                <div class="col-12 col-xl-7">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0" id="tabelaPedidosIndustria">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:75px">Ação</th>
                                    <th>Tipo</th>
                                    <th>Entrada</th>
                                    <th>Previsão</th>
                                    <th>Saída</th>
                                </tr>
                            </thead>
                            <tbody id="tabelaDados">
                                <tr id="pedidoSemDados">
                                    <td colspan="5" class="text-center text-muted py-3">Digite um pedido e pressione Tab para consultar as entradas.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalComentarioPedido" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-chat-left-text me-2"></i>Comentário do pedido</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="small text-muted mb-2" id="comentarioPedidoInfo"></div>
                <div id="listaComentariosPedido" class="mb-3"></div><label for="textoComentarioPedido" class="form-label">Novo comentário</label><textarea class="form-control" id="textoComentarioPedido" rows="4" maxlength="2000" placeholder="Digite o comentário..."></textarea>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="button" class="btn btn-primary" id="btnSalvarComentarioPedido"><i class="bi bi-save me-1"></i> Salvar comentário</button></div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEstornoPedido" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formEstornoPedido">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-arrow-counterclockwise me-2"></i>Estorno do pedido</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning py-2"><i class="bi bi-exclamation-triangle me-1"></i>Pedido: <strong id="estornoPedidoExibicao">-</strong></div>
                    <label class="form-label">Tipo de estorno</label>
                    <div class="row g-2 mb-3">
                        <div class="col-6"><input class="btn-check" type="radio" name="estorno_total_parcial" id="estornoParcial" value="parcial" autocomplete="off"><label class="btn btn-outline-primary w-100" for="estornoParcial"><i class="bi bi-dash-circle me-1"></i>Estorno parcial</label></div>
                        <div class="col-6"><input class="btn-check" type="radio" name="estorno_total_parcial" id="estornoTotal" value="total" autocomplete="off"><label class="btn btn-outline-primary w-100" for="estornoTotal"><i class="bi bi-check2-circle me-1"></i>Estorno total</label></div>
                    </div>
                    <div class="mb-3"><label for="estornoAtendimento" class="form-label">Atendimento</label><input type="text" class="form-control" id="estornoAtendimento" maxlength="6" required autocomplete="off"></div>
                    <div class="mb-3"><label for="estornoVendedor" class="form-label">Vendedor</label><select class="form-select" id="estornoVendedor" required>
                            <option value="">Selecione o vendedor...</option><?php foreach ($vendedoresEstorno as $v): ?><?php if ((int)$v['ativo'] === 1): ?><option value="<?= (int)$v['id'] ?>"><?= htmlspecialchars($v['nome'], ENT_QUOTES, 'UTF-8') ?></option><?php endif; ?><?php endforeach; ?>
                        </select></div>
                    <div><label for="estornoMotivo" class="form-label">Motivo</label><textarea class="form-control" id="estornoMotivo" maxlength="100" rows="3" required placeholder="Informe o motivo do estorno"></textarea>
                        <div class="form-text text-end"><span id="estornoMotivoContador">0</span>/100</div>
                    </div>
                    <div id="estornoErro" class="alert alert-danger mt-3 mb-0 d-none"></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-danger" id="btnConfirmarEstorno"><i class="bi bi-check-lg me-1"></i>Confirmar estorno</button></div>
            </form>
        </div>
    </div>
</div>

<style>
    #tabelaPedidosIndustria tbody tr.pedido-em-atraso td {
        color: var(--bs-danger) !important;
        background-color: rgba(var(--bs-danger-rgb), .06)
    }

    #tabelaPedidosIndustria tbody tr.pedido-sem-saida td {
        color: var(--bs-warning) !important;
        background-color: rgba(var(--bs-warning-rgb), .06)
    }

    #tabelaPedidosIndustria tbody tr.pedido-com-saida td {
        color: var(--bs-success) !important;
        background-color: rgba(var(--bs-success-rgb), .06)
    }

    #pedidoConsultaStatus .alert {
        margin-top: .5rem;
        margin-bottom: 0;
        padding: .55rem .75rem;
        font-size: .875rem;
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    #pedidoConsultaStatus .alert i {
        font-size: 1rem;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const campoPedido = document.getElementById('pedido'),
            tabela = document.getElementById('tabelaDados');
        const modalEl = document.getElementById('modalComentarioPedido'),
            modal = new bootstrap.Modal(modalEl),
            listaComentarios = document.getElementById('listaComentariosPedido'),
            textoComentario = document.getElementById('textoComentarioPedido'),
            infoComentario = document.getElementById('comentarioPedidoInfo'),
            btnSalvarComentario = document.getElementById('btnSalvarComentarioPedido');
        let pedidoIdComentario = null;

        function escapeHtml(v) {
            const d = document.createElement('div');
            d.textContent = v ?? '';
            return d.innerHTML
        }

        function formatarTipo(t) {
            const v = String(t ?? '').trim().toUpperCase();
            if (v === 'P') return 'Padrão';
            if (v === 'E') return 'Encomenda';
            if (v === 'T') return 'Telha';
            return v || '-'
        }

        function possuiSaida(r) {
            const s = String(r.saida ?? '').trim();
            return s !== '' && s !== '00-00-00' && s !== '00-00-00 00:00'
        }

        function formataTimeStamp(d) {
            if (!d) return '';
            const p = d.split(' ');
            if (!p[0]) return '';
            const a = p[0].split('-');
            if (a.length !== 3) return d;
            const h = (p[1] || '00:00').split(':');
            return `${a[2]}-${a[1]}-${a[0].slice(-2)} ${h[0]}:${h[1]||'00'}`
        }

        function formataData(d) {
            if (!d) return '';
            const a = String(d).split(' ')[0].split('-');
            return a.length === 3 ? `${a[2]}-${a[1]}-${a[0].slice(-2)}` : d
        }

        function formatarSaida(r) {
            if (!possuiSaida(r)) return 'Sem saída';
            const s = formataTimeStamp(r.saida),
                u = String(r.user_saida ?? '').trim();
            return u ? s + ' - ' + u : s
        }

        function atualizarIndicadorEstorno(registros) {
            const status = document.getElementById('pedidoConsultaStatus');
            const total = registros.length ? Number(registros[0].total_estornos || 0) : 0;

            if (total > 0) {
                status.innerHTML = '<div class="alert alert-danger border-danger-subtle shadow-sm" role="alert">' +
                    '<i class="bi bi-arrow-counterclockwise"></i>' +
                    '<span><strong>Atenção:</strong> este pedido possui <strong>' + total +
                    ' estorno' + (total === 1 ? '' : 's') + '</strong> registrado' + (total === 1 ? '' : 's') + '.</span>' +
                    '</div>';
            } else {
                status.innerHTML = '';
            }
        }

        function renderizarEntradas(registros) {
            atualizarIndicadorEstorno(registros);
            tabela.innerHTML = '';
            if (!registros.length) {
                tabela.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">Nenhuma entrada encontrada para este pedido.</td></tr>';
                return
            }
            registros.forEach(r => {
                const tr = document.createElement('tr'),
                    comentarios = Number(r.total_comentarios || 0),
                    temSaida = possuiSaida(r);
                tr.className = temSaida ? 'pedido-com-saida' : 'pedido-sem-saida';
                if (!temSaida) {
                    const a = String(r.previsao || '').split(' ')[0].split('-');
                    if (a.length === 3) {
                        const prev = new Date(a[0], a[1] - 1, a[2]);
                        const hoje = new Date();
                        hoje.setHours(0, 0, 0, 0);
                        if (prev < hoje) tr.className = 'pedido-em-atraso'
                    }
                }
                tr.innerHTML = `<td class="text-center"><button type="button" class="btn btn-sm btn-outline-primary btn-comentario-pedido" data-id="${escapeHtml(r.id)}" data-pedido="${escapeHtml(r.pedido)}" title="Adicionar comentário"><i class="bi bi-chat-left-text"></i>${comentarios?`<span class="badge text-bg-primary ms-1">${comentarios}</span>`:''}</button></td><td>${escapeHtml(formatarTipo(r.tipo))}</td><td>${escapeHtml(formataTimeStamp(r.entrada)+(r.user_entrada?' - '+r.user_entrada:''))}</td><td>${escapeHtml(formataData(r.previsao))}</td><td>${escapeHtml(formatarSaida(r))}</td>`;
                tabela.appendChild(tr)
            })
        }
        async function consultarPedido() {
            const pedido = campoPedido.value.trim();
            if (!pedido) {
                document.getElementById('pedidoConsultaStatus').innerHTML = '';
                tabela.innerHTML = '<tr id="pedidoSemDados"><td colspan="5" class="text-center text-muted py-3">Digite um pedido e pressione Tab para consultar as entradas.</td></tr>';
                return
            }
            try {
                const resp = await fetch('index.php?page=pedidos_buscar&pedido=' + encodeURIComponent(pedido), {
                    cache: 'no-store'
                });
                if (!resp.ok) throw new Error('HTTP ' + resp.status);
                const dados = await resp.json();
                renderizarEntradas(Array.isArray(dados) ? dados : [])
            } catch (e) {
                console.error(e);
                document.getElementById('pedidoConsultaStatus').innerHTML = '';
                tabela.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-3">Erro ao consultar o pedido.</td></tr>'
            }
        }
        campoPedido.addEventListener('keydown', e => {
            if (e.key === 'Tab') consultarPedido()
        });
        tabela.addEventListener('click', async e => {
            const b = e.target.closest('.btn-comentario-pedido');
            if (!b) return;
            pedidoIdComentario = b.dataset.id;
            infoComentario.textContent = 'Pedido ' + b.dataset.pedido + ' • Entrada #' + pedidoIdComentario;
            textoComentario.value = '';
            listaComentarios.innerHTML = '<div class="text-muted small">Carregando comentários...</div>';
            modal.show();
            try {
                const resp = await fetch('index.php?page=pedidos_comentario&acao=listar&pedido_id=' + encodeURIComponent(pedidoIdComentario), {
                    cache: 'no-store'
                });
                if (!resp.ok) throw new Error('HTTP ' + resp.status);
                const cs = await resp.json();
                if (!cs.length) {
                    listaComentarios.innerHTML = '<div class="alert alert-light border small mb-0">Nenhum comentário cadastrado para esta entrada.</div>';
                    return
                }
                listaComentarios.innerHTML = cs.map(c => `<div class="border rounded p-2 mb-2"><div class="small text-muted mb-1">${escapeHtml(c.usuario||'Usuário')} • ${escapeHtml(c.criado_em)}</div><div>${escapeHtml(c.comentario).replace(/\n/g,'<br>')}</div></div>`).join('')
            } catch (e) {
                console.error(e);
                listaComentarios.innerHTML = '<div class="text-danger small">Não foi possível carregar os comentários.</div>'
            }
        });
        btnSalvarComentario.addEventListener('click', async () => {
            const comentario = textoComentario.value.trim();
            if (!pedidoIdComentario || !comentario) {
                textoComentario.focus();
                return
            }
            btnSalvarComentario.disabled = true;
            try {
                const fd = new FormData();
                fd.append('pedido_id', pedidoIdComentario);
                fd.append('comentario', comentario);
                const resp = await fetch('index.php?page=pedidos_comentario', {
                        method: 'POST',
                        body: fd
                    }),
                    resultado = await resp.json();
                if (!resp.ok || !resultado.sucesso) throw new Error(resultado.erro || 'Falha ao salvar');
                textoComentario.value = '';
                modal.hide();
                await consultarPedido()
            } catch (e) {
                console.error(e);
                alert(e.message || 'Não foi possível salvar o comentário.')
            } finally {
                btnSalvarComentario.disabled = false
            }
        });

        const btnEstorno = document.getElementById('btn_estorno'),
            modalEstornoEl = document.getElementById('modalEstornoPedido'),
            modalEstorno = new bootstrap.Modal(modalEstornoEl),
            formEstorno = document.getElementById('formEstornoPedido'),
            pedidoEstornoExibicao = document.getElementById('estornoPedidoExibicao'),
            atendimentoEstorno = document.getElementById('estornoAtendimento'),
            vendedorEstorno = document.getElementById('estornoVendedor'),
            motivoEstorno = document.getElementById('estornoMotivo'),
            contadorMotivo = document.getElementById('estornoMotivoContador'),
            erroEstorno = document.getElementById('estornoErro'),
            btnConfirmarEstorno = document.getElementById('btnConfirmarEstorno');
        btnEstorno.addEventListener('click', function() {
            const pedido = campoPedido.value.trim();
            if (!pedido) {
                campoPedido.focus();
                return
            }
            pedidoEstornoExibicao.textContent = pedido;
            formEstorno.reset();
            pedidoEstornoExibicao.textContent = pedido;
            contadorMotivo.textContent = '0';
            erroEstorno.classList.add('d-none');
            modalEstorno.show()
        });
        motivoEstorno.addEventListener('input', () => contadorMotivo.textContent = motivoEstorno.value.length);
        formEstorno.addEventListener('submit', async function(e) {
            e.preventDefault();
            erroEstorno.classList.add('d-none');
            const tipo = document.querySelector('input[name="estorno_total_parcial"]:checked');
            const pedido = campoPedido.value.trim(),
                atendimento = atendimentoEstorno.value.trim(),
                vendedorId = vendedorEstorno.value,
                motivo = motivoEstorno.value.trim();
            if (!tipo) {
                erroEstorno.textContent = 'Selecione estorno parcial ou total.';
                erroEstorno.classList.remove('d-none');
                return
            }
            if (!pedido) {
                erroEstorno.textContent = 'Informe o pedido.';
                erroEstorno.classList.remove('d-none');
                return
            }
            if (!atendimento) {
                erroEstorno.textContent = 'Informe o atendimento.';
                erroEstorno.classList.remove('d-none');
                atendimentoEstorno.focus();
                return
            }
            if (!vendedorId) {
                erroEstorno.textContent = 'Selecione o vendedor.';
                erroEstorno.classList.remove('d-none');
                vendedorEstorno.focus();
                return
            }
            if (!motivo) {
                erroEstorno.textContent = 'Informe o motivo.';
                erroEstorno.classList.remove('d-none');
                motivoEstorno.focus();
                return
            }
            btnConfirmarEstorno.disabled = true;
            try {
                const fd = new FormData();
                fd.append('pedido', pedido);
                fd.append('atendimento', atendimento);
                fd.append('vendedor_id', vendedorId);
                fd.append('total_parcial', tipo.value);
                fd.append('motivo', motivo);
                const resp = await fetch('index.php?page=pedidos_estorno', {
                        method: 'POST',
                        body: fd
                    }),
                    resultado = await resp.json();
                if (!resp.ok || !resultado.sucesso) throw new Error(resultado.erro || 'Não foi possível registrar o estorno.');
                modalEstorno.hide();
                formEstorno.reset();
                alert('Estorno registrado com sucesso.')
            } catch (err) {
                erroEstorno.textContent = err.message;
                erroEstorno.classList.remove('d-none')
            } finally {
                btnConfirmarEstorno.disabled = false
            }
        });
    });
</script>