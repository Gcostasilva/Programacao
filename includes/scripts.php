<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.3.8/js/dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"></script>
<script
    src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0/dist/js/adminlte.min.js"></script>

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

<!-- Função para preencher o modal  -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalEditar = document.getElementById('modalEditar');
        const formEditar = document.getElementById('formEditar');

        // Dispara quando o modal está prestes a abrir
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
                    document.getElementById('edit_data').value = registro.data;
                    document.getElementById('edit_pedido').value = registro.pedido;
                    document.getElementById('edit_espessura').value = registro.espessura;
                    document.getElementById('edit_aco').value = registro.aco;
                    document.getElementById('edit_vendedor').value = registro.vendedor;
                    document.getElementById('edit_peso').value = registro.peso;
                    document.getElementById('edit_peso_real').value = registro.peso_realizado;
                    document.getElementById('edit_obs').value = registro.obs;
                })
                .catch(function (erro) {
                    alert('Não foi possível carregar o registro para edição.');
                    console.error(erro);
                });
        });

        // Envio do formulário de edição
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
                    // Recarrega a página para a tabela refletir a mudança
                    location.reload();
                })
                .catch(function (erro) {
                    alert('Não foi possível salvar a edição.');
                    console.error(erro);
                });
        });
    });
    const campo = document.getElementById('edit_peso_real');
    campo.focus();
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
                        renderizarResultados(Array.isArray(dados) ? dados : []);
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

        // Se o usuário fechar o modal no meio do processo, garante estado limpo na próxima abertura
        modalBaixar.addEventListener('hidden.bs.modal', resetarModal);

    });
</script>