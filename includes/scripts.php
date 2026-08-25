<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.3.8/js/dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script
    src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>


<script src="assets/js/enter-como-tab.js"></script>

<!-- Filtro de equipamento e data para o formulário diária -->
<script>
    // Seleciona os inputs e a tabela
    const recurso = document.getElementById('recurso');
    const data = document.getElementById('data');
    const tabelaDados = document.getElementById('tabelaDados');

    // Função de filtragem
    function filtrarTabela() {
        // Pega o TEXTO da opção selecionada, não o value (que é o ID)
        const opcaoSelecionada = recurso.options[recurso.selectedIndex];
        const inPrecurso = opcaoSelecionada ? opcaoSelecionada.text.toLowerCase().trim() : '';

        let inPdata = '';
        if (data.value) {
            const [ano, mes, dia] = data.value.split('-');
            inPdata = `${dia}/${mes}/${ano}`;
        }

        const linhas = tabelaDados.getElementsByTagName('tr');

        for (let i = 0; i < linhas.length; i++) {
            const linha = linhas[i];
            const colunas = linha.getElementsByTagName('td');

            if (colunas.length > 0) {
                const txtData = colunas[0].textContent.toLowerCase();
                const txtRecurso = colunas[1].textContent.toLowerCase();

                const corrRecurso = inPrecurso === '' || inPrecurso === 'selecione...' || txtRecurso.includes(inPrecurso);
                const corrData = inPdata === '' || txtData.includes(inPdata);

                linha.style.display = (corrRecurso && corrData) ? '' : 'none';
            }
        }
    }

    // Eventos para acionar o filtro
    data.addEventListener('change', filtrarTabela);
    data.addEventListener('input', filtrarTabela); // 'input' responde instantaneamente ao digitar a data
    recurso.addEventListener('change', filtrarTabela);
    recurso.addEventListener('input', filtrarTabela); // 'input' para filtrar enquanto digita no text/select
</script>

<!-- Função para recolher os cards  -->
<script>
    //script para adicionar o toggle button nos formulários de inserção
    document.addEventListener('DOMContentLoaded', function () {

        // Constantes com os elementos de cada card — fica fácil adicionar um novo card aqui
        const CARD_SEMANAL = {
            card: document.getElementById('cardFormSemanal'),
            btn: document.getElementById('btnToggleFormSemanal'),
            icone: document.getElementById('iconeToggleFormSemanal'),
            chave: 'formSemanal_aberto'
        };
        const CARD_DIARIA = {
            card: document.getElementById('cardFormDiaria'),
            btn: document.getElementById('btnToggleFormDiaria'),
            icone: document.getElementById('iconeToggleFormDiaria'),
            chave: 'formDiaria_aberto'
        };
        const CARD_QUINZENA = {
            card: document.getElementById('cardFormQuinzena'),
            btn: document.getElementById('btnToggleFormQuinzena'),
            icone: document.getElementById('iconeToggleFormQuinzena'),
            chave: 'formQuinzena_aberto'
        };


        // Função genérica: aplica o estado (aberto/fechado) em UM card específico
        function aplicarEstado(config, aberto) {
            if (aberto) {
                config.card.classList.remove('collapsed-card');
                config.icone.classList.replace('bi-chevron-down', 'bi-chevron-up');
            } else {
                config.card.classList.add('collapsed-card');
                config.icone.classList.replace('bi-chevron-up', 'bi-chevron-down');
            }
        }

        // Função genérica: alterna UM card específico e salva o estado dele
        function alternar(config) {
            const estaAberto = !config.card.classList.contains('collapsed-card');
            const novoEstado = !estaAberto;

            aplicarEstado(config, novoEstado);
            localStorage.setItem(config.chave, novoEstado ? '1' : '0');
        }

        // Função que "liga" tudo pra um card: estado inicial + eventos de clique
        function inicializarCard(config) {
            // Se o card não existe nesta página, não faz nada (evita erro)
            if (!config.card) {
                return;
            }
            const header = config.card.querySelector('.card-header');

            // Estado inicial: lê o localStorage; se nunca salvou, começa fechado
            const salvo = localStorage.getItem(config.chave);
            aplicarEstado(config, salvo === '1');

            // Clicar no título alterna esse card
            header.addEventListener('click', function () {
                alternar(config);
            });

            // Clicar no botão alterna esse card, sem deixar o clique vazar pro header
            config.btn.addEventListener('click', function (e) {
                e.stopPropagation();
                alternar(config);
            });
        }

        // Inicializa cada card independentemente
        inicializarCard(CARD_DIARIA);
        inicializarCard(CARD_SEMANAL);
        inicializarCard(CARD_QUINZENA);
    });
</script>

<!-- Função para preencher o modal de telha -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalEditar = document.getElementById('modalEditar');
        const formEditar = document.getElementById('formEditar');

        modalEditar.addEventListener('show.bs.modal', function (event) {
            const botao = event.relatedTarget;
            const id = botao.dataset.id;

            fetch("index.php?page=prog_diaria_buscar&id=" + id)
                .then(function (resposta) {
                    if (!resposta.ok) {
                        throw new Error('Erro ao buscar registro');
                    }
                    return resposta.json();
                })
                .then(function (registro) {
                    document.getElementById('edit_id').value = registro.id;
                    document.getElementById('edit_recurso').value = registro.maquina_id;
                    document.getElementById('edit_data').value = registro.data_programacao;
                    document.getElementById('edit_pedido').value = registro.pedido;
                    document.getElementById('edit_espessura').value = registro.espessura;
                    document.getElementById('edit_aco').value = registro.aco;
                    document.getElementById('edit_vendedor').value = registro.vendedor_id; // era registro.vendedor
                    document.getElementById('edit_peso').value = registro.peso;
                    document.getElementById('edit_peso_real').value = registro.peso_realizado;
                    // document.getElementById('falta_mp_at').checked = !!registro.falta_mp; // coluna não vem mais na consulta
                    document.getElementById('edit_obs').value = registro.obs;

                    // Foco no campo, agora dentro do momento certo (quando o modal abre)
                    document.getElementById('edit_peso_real').focus();
                })
                .catch(function (erro) {
                    alert('Não foi possível carregar o registro para edição.');
                    console.error(erro);
                });
        });

        formEditar.addEventListener('submit', function (event) {
            event.preventDefault();

            const formData = new FormData(formEditar);

            fetch('index.php?page=prog_diaria_editar', {
                method: 'POST',
                body: formData
            })
                .then(function (resposta) {
                    if (!resposta.ok) {
                        throw new Error('Erro ao atualizar');
                    }
                    location.reload();
                })
                .catch(function (erro) {
                    alert('Não foi possível salvar a edição.');
                    console.error(erro);
                });
        });
    });
</script>
<!-- Função para preencher o modal baixa de telha  -->
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const modalBaixar = document.getElementById('modalBaixar');

        if (!modalBaixar) {
            return;
        }

        const inputPedido = document.getElementById('baixa_pedido');
        const divResultados = document.getElementById('baixa_resultados');
        const inputId = document.getElementById('baixa_id');
        const inputPesoReal = document.getElementById('baixa_peso_real');
        const btnAtualizar = document.getElementById('baixa_btn_atualizar');
        const form = document.getElementById('formBaixar');

        const camposConferencia = {
            recurso: document.getElementById('baixa_recurso'),
            data: document.getElementById('baixa_data'),
            espessura: document.getElementById('baixa_espessura'),
            aco: document.getElementById('baixa_aco'),
            vendedor: document.getElementById('baixa_vendedor'),
            peso: document.getElementById('baixa_peso'),
            falta_mp: document.getElementById('falta_mp_at'),
            observacao: document.getElementById('baixa_obs'),
        };

        let debounceTimer = null;

        // Limpa a seleção atual (id, peso real e campos de conferência)
        function limparSelecao() {

            inputId.value = '';
            inputPesoReal.value = '';
            inputPesoReal.disabled = true;
            btnAtualizar.disabled = true;

            Object.values(camposConferencia).forEach(campo => {
                campo.value = '';
            });
        }

        // Reseta o modal inteiro para começar uma nova baixa (sem fechar)
        function resetarModal() {

            inputPedido.value = '';
            divResultados.innerHTML = '';
            limparSelecao();
            inputPedido.focus();
        }

        // Desenha a lista de registros encontrados abaixo do campo Pedido
        function renderizarResultados(registros) {

            divResultados.innerHTML = '';

            if (registros.length === 0) {
                divResultados.innerHTML = '<div class="list-group-item text-muted small">Nenhum registro encontrado</div>';
                return;
            }

            registros.forEach(registro => {

                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'list-group-item list-group-item-action small';

                const jaBaixado = registro.peso_real && Number(registro.peso_real) > 0;

                item.innerHTML =
                    (registro.recurso ?? '') + ' | Esp ' + registro.espessura +
                    ' | ' + registro.aco + ' | Peso ' + registro.peso +
                    (jaBaixado ? ' <span class="badge bg-success">já baixado</span>' : '');

                item.addEventListener('click', function () {
                    selecionarRegistro(registro);
                });

                divResultados.appendChild(item);
            });
        }

        // Preenche os campos de conferência com o registro escolhido na lista
        function selecionarRegistro(registro) {

            inputId.value = registro.id;

            camposConferencia.recurso.value = registro.recurso_id;
            camposConferencia.data.value = registro.data_programacao;
            camposConferencia.espessura.value = registro.espessura;
            camposConferencia.aco.value = registro.aco;
            camposConferencia.vendedor.value = registro.vendedor_id;
            camposConferencia.peso.value = registro.peso;
            camposConferencia.falta_mp.checked = !!registro.falta_mp;
            camposConferencia.observacao.value = registro.obs;

            divResultados.innerHTML = '';

            inputPesoReal.disabled = false;
            btnAtualizar.disabled = false;
            inputPesoReal.focus();
        }

        // Busca por pedido enquanto o usuário digita (com debounce)
        inputPedido.addEventListener('input', function () {

            clearTimeout(debounceTimer);

            const pedido = inputPedido.value.trim();

            limparSelecao();

            if (pedido.length < 2) {
                divResultados.innerHTML = '';
                return;
            }

            debounceTimer = setTimeout(function () {

                fetch('index.php?page=prog_diaria_baixar&pedido=' + encodeURIComponent(pedido))
                    .then(response => response.json())
                    .then(dados => {

                        const registros = Array.isArray(dados) ? dados : [];

                        if (registros.length === 1) {
                            // Só um resultado: seleciona automaticamente, sem exigir clique
                            selecionarRegistro(registros[0]);
                        } else {
                            renderizarResultados(registros);
                        }
                    })
                    .catch(erro => {
                        console.error('Erro ao buscar pedido:', erro);
                    });

            }, 400);
        });

        // Envia a atualização do peso real
        form.addEventListener('submit', function (e) {

            e.preventDefault();

            if (!inputId.value) {
                alert('Selecione um registro na lista antes de atualizar.');
                return;
            }

            const dadosForm = new FormData();
            dadosForm.append('id', inputId.value);
            dadosForm.append('peso_real', inputPesoReal.value);

            fetch('index.php?page=prog_diaria_baixa_salvar', {
                method: 'POST',
                body: dadosForm
            })
                .then(response => response.json())
                .then(resposta => {

                    if (resposta.sucesso) {
                        // Mantém o modal aberto e pronto para a próxima baixa
                        resetarModal();
                    } else {
                        alert(resposta.erro ?? 'Erro ao atualizar');
                    }
                })
                .catch(erro => {
                    console.error('Erro ao atualizar:', erro);
                });
        });
        modalBaixar.addEventListener('shown.bs.modal', function () {
            const campo = document.getElementById('baixa_pedido');
            setTimeout(function () { campo.focus(); }, 100);
        });
        // Se o usuário fechar o modal no meio do processo, garante estado limpo na próxima abertura
        modalBaixar.addEventListener('hidden.bs.modal', resetarModal);

    });
</script>
<!-- Função para classificar a tabela de programação diária  -->
<script>

    document.addEventListener('DOMContentLoaded', function () {
        const tbody = document.getElementById('tabelaDados');

        if (!tbody) return; // guarda de segurança, igual você já faz com os cards

        Sortable.create(tbody, {
            animation: 150,
            handle: 'td:first-child', // arraste iniciado pela primeira célula (data). Remova esta linha se quiser arrastar de qualquer ponto da linha

            // Impede soltar a linha num ponto onde a data seja diferente
            onMove: function (evt) {
                const dataOrigem = evt.dragged.dataset.data;
                const dataDestino = evt.related.dataset.data;
                return dataOrigem === dataDestino;
            },

            onEnd: function () {
                // Depois de soltar, pega todas as linhas do MESMO dia da linha movida
                // e envia a nova ordem pro backend
                const linhas = Array.from(tbody.querySelectorAll('tr'));

                // Agrupa por data, mantendo a ordem atual do DOM
                const grupos = {};
                linhas.forEach(function (linha, index) {
                    const data = linha.dataset.data;
                    if (!grupos[data]) grupos[data] = [];
                    grupos[data].push({ id: linha.dataset.id, posicao: grupos[data].length });
                });

                // Envia cada grupo (normalmente só um vai ter mudado, mas é seguro reenviar todos)
                Object.values(grupos).forEach(function (grupo) {
                    salvarOrdem(grupo);
                });
            }
        });

        function salvarOrdem(itens) {
            fetch(`index.php?page=prog_diaria_reordenar`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ itens: itens })
            })
                .then(response => response.json())
                .then(data => {
                    if (!data.sucesso) {
                        console.error('Erro ao salvar ordem:', data.mensagem);
                    }
                })
                .catch(error => console.error('Erro na requisição:', error));
        }
    });

</script>

<!-- Função para filtrar a tabela de programação diária  -->
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const checkbox = document.getElementById('exibir_baixados');
        const tbody = document.getElementById('tabelaDados');

        if (!checkbox || !tbody) return;

        checkbox.addEventListener('change', function () {

            const exibir = checkbox.checked ? '1' : '0';

            fetch(`index.php?page=prog_diaria_filtrar&exibir_baixados=${exibir}`)
                .then(function (response) {
                    return response.text();
                })
                .then(function (html) {
                    tbody.innerHTML = html;
                })
                .catch(function (erro) {
                    console.error('Erro ao filtrar programação:', erro);
                });
        });
    });
</script>


<!--                         Semanal                 -->


<!-- Função para preencher a descrição produto ao sair do codigo -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const codigoInput = document.getElementById('codigo');
        const descricaoInput = document.getElementById('descricao_sem');
        const pesoLiquidoInput = document.getElementById('peso_liquido');

        codigoInput.addEventListener('blur', function () {
            const codigo = this.value;
            if (codigo) {
                fetch(`index.php?page=prog_semanal_buscarCodigo&codigo=${codigo}`)
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (data) {
                        console.log('Resposta:', data);

                        if (data.descricao !== undefined) {
                            descricaoInput.value = data.descricao;
                            pesoLiquidoInput.value = data.peso_liquido;
                        }
                    })
                    .catch(function (error) {
                        console.error('Erro ao buscar descrição:', error);
                    });
            }
            // Exibe o modal de cálculo quando o peso líquido for menor que 1.1
            if (parseFloat(pesoLiquidoInput.value) < 1.1) {
                if (codigoInput.value.substring(0, 2) === '03' || codigoInput.value.substring(0, 2) === '06') {
                    const modal = new bootstrap.Modal(document.getElementById('modalCalculo_chapa'));
                    setTimeout(function () { modal.show(); }, 50);
                       
                } else {

                    const modal = new bootstrap.Modal(document.getElementById('modalCalculo_perfil'));
                    setTimeout(function () { modal.show(); }, 50);
                }
            }
        });
    });
</script>

<!-- Função para multiplicar peso líquido por quantidade -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const quantidadeInput = document.querySelector('input[name="quantidade"]');
        const pesoLiquidoInput = document.getElementById('peso_liquido');
        const pesoInput = document.querySelector('input[name="peso"]');

        quantidadeInput.addEventListener('input', function () {
            const quantidade = parseFloat(this.value);
            const pesoLiquido = parseFloat(pesoLiquidoInput.value);

            if (!isNaN(quantidade) && !isNaN(pesoLiquido)) {
                pesoInput.value = (quantidade * pesoLiquido).toFixed(2);
            } else {
                pesoInput.value = '';
            }
        });
    });
</script>

<!-- Função para Dividir peso pelo peso liquido -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const quantidadeInput = document.querySelector('input[name="quantidade"]');
        const pesoLiquidoInput = document.getElementById('peso_liquido');
        const pesoInput = document.querySelector('input[name="peso"]');

        pesoInput.addEventListener('input', function () {
            const quantidade = parseFloat(this.value);
            const pesoLiquido = parseFloat(pesoLiquidoInput.value);

            if (!isNaN(quantidade) && !isNaN(pesoLiquido)) {
                quantidadeInput.value = (quantidade / pesoLiquido).toFixed(0);
            } else {
                quantidadeInput.value = '';
            }
        });
    });
</script>

<!-- Função para limitar a data com base na semana -->
<script>

    document.addEventListener('DOMContentLoaded', function () {

        const semanaInput = document.getElementById('semana');
        const dataInput = document.getElementById('data');

        function aplicarLimitesSemana() {

            const valor = semanaInput.value;

            if (!valor) {
                dataInput.removeAttribute('min');
                dataInput.removeAttribute('max');
                return;
            }

            // Exemplo: 2026-W34
            const [ano, semana] = valor.split('-W').map(Number);

            // Janeiro 4 sempre pertence à semana ISO 1
            const janeiro4 = new Date(ano, 0, 4);

            // Dia da semana (domingo = 0)
            const diaSemana = janeiro4.getDay() || 7;

            // Segunda-feira da semana 1
            const segundaSemana1 = new Date(janeiro4);
            segundaSemana1.setDate(
                janeiro4.getDate() - diaSemana + 1
            );

            // Segunda-feira da semana selecionada
            const inicio = new Date(segundaSemana1);
            inicio.setDate(
                segundaSemana1.getDate() + (semana - 1) * 7
            );

            // Domingo da semana selecionada
            const fim = new Date(inicio);
            fim.setDate(inicio.getDate() + 6);

            // Formata para YYYY-MM-DD
            function formatarData(data) {
                const ano = data.getFullYear();
                const mes = String(data.getMonth() + 1).padStart(2, '0');
                const dia = String(data.getDate()).padStart(2, '0');

                return `${ano}-${mes}-${dia}`;
            }

            const dataInicio = formatarData(inicio);
            const dataFim = formatarData(fim);

            // Limita o input date
            dataInput.min = dataInicio;
            dataInput.max = dataFim;

            // Se a data atual estiver fora da semana, limpa
            if (
                dataInput.value &&
                (
                    dataInput.value < dataInicio ||
                    dataInput.value > dataFim
                )
            ) {
                dataInput.value = '';
            }

            console.log('Semana:', valor);
            console.log('Início:', dataInicio);
            console.log('Fim:', dataFim);
        }

        // Roda uma vez no carregamento, tratando o valor padrão do campo (se houver)
        aplicarLimitesSemana();

        // Continua rodando a cada mudança do usuário
        semanaInput.addEventListener('input', aplicarLimitesSemana);

    });
</script>

<!-- Função para preencher o modal  -->
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const modalEditarSemanal = document.getElementById('modalEditarSemanal');
        const formEditarSemanal = document.getElementById('formEditarSemanal');

        modalEditarSemanal.addEventListener('show.bs.modal', function (event) {
            const botao = event.relatedTarget;
            const id = botao.dataset.id;

            fetch("index.php?page=prog_semanal_buscar&id=" + id)
                .then(function (resposta) {
                    if (!resposta.ok) {
                        throw new Error('Erro ao buscar registro');
                    }
                    return resposta.json();
                })
                .then(function (registro) {
                    document.getElementById('edit_sem_id').value = registro.id;
                    document.getElementById('edit_sem_recurso').value = registro.maquina_id;
                    document.getElementById('edit_sem_data').value = registro.data_programacao;
                    document.getElementById('edit_sem_demanda').value = registro.demanda;
                    document.getElementById('edit_sem_codigo_s').value = registro.produto_id;
                    document.getElementById('edit_sem_descricao').value = registro.descricao;
                    document.getElementById('edit_sem_quantidade').value = registro.qtd;
                    document.getElementById('edit_sem_peso').value = registro.peso;
                    document.getElementById('edit_sem_descricao').value = registro.descricao;
                    document.getElementById('edit_sem_peso_liquido').value = registro.peso_liquido;
                    if (registro.peca_realizada > 0) {
                        document.getElementById('edit_sem_quantidade_realizada').value = registro.peca_realizada;
                    } else {
                        document.getElementById('edit_sem_quantidade_realizada').value = '';
                    }
                    document.getElementById('edit_sem_peso_realizado').value = registro.peso_realizado;
                    document.getElementById('edit_sem_observacao').value = registro.obs;
                })
                .catch(function (erro) {
                    alert('Não foi possível carregar o registro para edição.');
                    console.error(erro);
                });
        });

        formEditarSemanal.addEventListener('submit', function (event) {
            event.preventDefault();

            const formData = new FormData(formEditarSemanal);

            fetch('index.php?page=prog_semanal_editar', {
                method: 'POST',
                body: formData
            })
                .then(function (resposta) {
                    if (!resposta.ok) {
                        throw new Error('Erro ao atualizar');
                    }
                    location.reload();
                })
                .catch(function (erro) {
                    alert('Não foi possível salvar a edição.');
                    console.error(erro);
                });
        });
        modalEditarSemanal.addEventListener('shown.bs.modal', function () {
            const campo = document.getElementById('edit_sem_quantidade_realizada');
            setTimeout(function () { campo.focus(); }, 100);
        });
    });
</script>

<!-- Função para multiplicar peso líquido por quantidade dentro do modal -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editarquantidadeInput = document.querySelector('input[name="peca_realizada"]');
        const editarpesoInput = document.querySelector('input[name="peso_realizado"]');
        const editarpesoLiquidoInput = document.getElementById('edit_sem_peso_liquido');

        editarquantidadeInput.addEventListener('input', function () {
            const quantidade_sem = parseFloat(this.value);
            const pesoLiquido_sem = parseFloat(editarpesoLiquidoInput.value);

            if (!isNaN(quantidade_sem) && !isNaN(pesoLiquido_sem)) {
                editarpesoInput.value = (quantidade_sem * pesoLiquido_sem).toFixed(2);
            } else {
                editarpesoInput.value = '';
            }
        });
    });
</script>

<!-- Filtro de equipamento e data para o formulário semanal -->
<script>
    document.getElementById('recurso_filtro').addEventListener('change', function () {
        const recursoSelecionado = this.value;
        const linhas = document.querySelectorAll('#tabelaSemanal tr');

        linhas.forEach(function (linha) {
            if (recursoSelecionado === '' || linha.dataset.recurso === recursoSelecionado) {
                linha.style.display = '';
            } else {
                linha.style.display = 'none';
            }
        });
    });
</script>sim

<!-- Função para ajustar a semana -->
<script>
    function adjustWeek(direction) {
        const weekInput = document.getElementById('semana_filtro');

        if (direction === 1) {
            weekInput.stepUp(); // Increments by 1 week
        } else if (direction === -1) {
            weekInput.stepDown(); // Decrements by 1 week
        }
    }
</script>

<!-- função para ordenar a tabela semanal -->
<script>
    function inicializarSortableSemanal() {

        const tbodys = document.querySelectorAll('.tabelaSemanal');

        tbodys.forEach(function (tbody) {
            Sortable.create(tbody, {
                animation: 150,
                handle: 'td:first-child',
                group: 'semanal', // MESMO nome em todos os tbody -> permite mover entre eles

                onEnd: function (evt) {
                    const linhaMovida = evt.item;
                    const tbodyDestino = evt.to;   // <tbody> onde a linha caiu
                    const tbodyOrigem = evt.from;  // <tbody> de onde ela saiu

                    const novaData = tbodyDestino.dataset.data;
                    const dataAnterior = linhaMovida.dataset.data; // ainda guarda o valor antigo, por enquanto

                    // Atualiza a data "guardada" na própria linha, para futuras leituras
                    linhaMovida.dataset.data = novaData;

                    salvarOrdemEData(tbodyDestino);

                    // Se a linha saiu de um dia para outro, o dia de origem também
                    // perdeu uma posição e precisa reordenar o que sobrou nele
                    if (tbodyOrigem !== tbodyDestino) {
                        salvarOrdemEData(tbodyOrigem);
                    }
                }
            });
        });

        function salvarOrdemEData(tbody) {
            const novaData = tbody.dataset.data;
            const linhas = Array.from(tbody.querySelectorAll('tr'));

            const itens = linhas.map(function (linha, index) {
                return {
                    id: linha.dataset.id,
                    posicao: index,
                    data: novaData
                };
            });

            if (itens.length === 0) return; // dia ficou vazio, nada pra salvar

            fetch('index.php?page=prog_semanal_reordenar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ itens: itens })
            })
                .then(response => response.json())
                .then(data => {
                    if (!data.sucesso) {
                        console.error('Erro ao salvar ordem:', data.mensagem);
                    }
                })
                .catch(error => console.error('Erro na requisição:', error));
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        inicializarSortableSemanal();
    });
</script>

<!-- Função para alternar tema claro/escuro/auto -->
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const themeButtons = document.querySelectorAll('[data-bs-theme-value]');
        const html = document.documentElement;
        const storageKey = 'theme';

        function aplicarTema(theme) {

            if (theme === 'auto') {

                const prefersDark = window.matchMedia(
                    '(prefers-color-scheme: dark)'
                ).matches;

                html.setAttribute(
                    'data-bs-theme',
                    prefersDark ? 'dark' : 'light'
                );

            } else {

                html.setAttribute('data-bs-theme', theme);

            }
        }


        // ============================
        // CARREGAR TEMA SALVO
        // ============================

        const temaSalvo = localStorage.getItem(storageKey);

        if (temaSalvo) {

            aplicarTema(temaSalvo);

        } else {

            // Primeiro acesso: automático
            aplicarTema('auto');

        }


        // ============================
        // CLIQUE NOS BOTÕES
        // ============================

        themeButtons.forEach(function (button) {

            button.addEventListener('click', function () {

                const theme = this.getAttribute('data-bs-theme-value');

                // Aplica o tema
                aplicarTema(theme);

                // Salva a preferência
                localStorage.setItem(storageKey, theme);

            });

        });


        // ============================
        // AUTO → ACOMPANHAR WINDOWS
        // ============================

        const mediaQuery = window.matchMedia(
            '(prefers-color-scheme: dark)'
        );

        mediaQuery.addEventListener('change', function () {

            const temaSalvo = localStorage.getItem(storageKey);

            if (temaSalvo === 'auto') {

                aplicarTema('auto');

            }

        });

    });
</script>

<!-- Função para logica de ajustar a semana do filtro semanal -->
<script>
    function adjustWeek(direcao) {
        const input = document.getElementById('semana_filtro');
        const [ano, semana] = input.value.split('-W').map(Number);

        // Converte "ano + número da semana ISO" para uma data real (quinta-feira daquela semana,
        // que é um truque comum pra evitar problemas de virada de ano)
        const data = new Date(ano, 0, 1 + (semana - 1) * 7);
        const diaSemana = data.getDay() || 7;
        data.setDate(data.getDate() + (4 - diaSemana)); // ajusta para a quinta-feira da semana

        // Aplica o incremento/decremento (em semanas, ou seja, 7 dias)
        data.setDate(data.getDate() + direcao * 7);

        // Recalcula ano e número da semana ISO a partir da nova data
        const novoAno = data.getFullYear();
        const inicioAno = new Date(novoAno, 0, 1);
        const novaSemana = Math.ceil((((data - inicioAno) / 86400000) + inicioAno.getDay() + 1) / 7);

        input.value = `${novoAno}-W${String(novaSemana).padStart(2, '0')}`;

        carregarSemana(); // dispara a busca dos novos dados (função do passo 3)
    }
</script>

<!-- Função para realizar a busca do filtro semanal -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputSemana = document.getElementById('semana_filtro');
        const inputRecurso = document.getElementById('recurso_filtro');
        inputSemana.addEventListener('change', carregarSemana);

        if (inputRecurso) {
            inputRecurso.addEventListener('change', carregarSemana);
        }
    });

    function carregarSemana() {

        const semana = document.getElementById('semana_filtro').value;
        const recursoElement = document.getElementById('recurso_filtro');
        const recurso = recursoElement ? recursoElement.value : '';
        const params = new URLSearchParams();

        params.append('page', 'prog_semanal_filtrar');
        params.append('semana', semana);

        if (recurso !== '') {
            params.append('recurso_filtro', recurso);
        }
        fetch('index.php?' + params.toString())
            .then(function (resposta) {
                if (!resposta.ok) {
                    throw new Error('Erro ao buscar semana');
                }
                return resposta.text();
            })
            .then(function (html) {
                document.getElementById('areaSemanal').innerHTML = html;
                inicializarSortableSemanal();
            })
            .catch(function (erro) {

                alert('Não foi possível carregar a semana selecionada.');
                console.error(erro);
            });
    }
</script>