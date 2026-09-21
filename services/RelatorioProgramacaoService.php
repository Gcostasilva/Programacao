<?php

require_once __DIR__ . '/../models/RelatorioProgramacaoModel.php';

/**
 * Organiza os dados brutos da programação em uma estrutura própria para
 * relatórios. A camada de apresentação (HTML/PDF) não deve precisar conhecer
 * a estrutura da tabela programacao.
 */
class RelatorioProgramacaoService
{
    private RelatorioProgramacaoModel $model;

    public function __construct(?RelatorioProgramacaoModel $model = null)
    {
        $this->model = $model ?? new RelatorioProgramacaoModel();
    }

    /**
     * Gera a estrutura consolidada do relatório de programação.
     *
     * Estrutura:
     * - periodo
     * - equipamentos
     *   - id / nome
     *   - totais
     *   - dias
     *     - data
     *     - totais
     *     - itens
     *
     * @return array<string, mixed>
     */
    public function gerar(
        string $dataInicio,
        string $dataFim,
        ?int $recursoId = null
    ): array {
        $registros = $this->model->listarProgramacao(
            $dataInicio,
            $dataFim,
            $recursoId
        );

        $relatorio = [
            'periodo' => [
                'inicio' => $dataInicio,
                'fim' => $dataFim,
            ],
            'totais' => [
                'quantidade' => 0,
                'peso_estimado' => 0.0,
                'pecas_realizadas' => 0,
                'peso_realizado' => 0.0,
            ],
            'equipamentos' => [],
        ];

        foreach ($registros as $registro) {
            $equipamentoId = (int) $registro['recurso_id'];
            $data = (string) $registro['data'];

            if (!isset($relatorio['equipamentos'][$equipamentoId])) {
                $relatorio['equipamentos'][$equipamentoId] = [
                    'id' => $equipamentoId,
                    'nome' => $registro['recurso'] ?: 'Equipamento não informado',
                    'totais' => $this->totaisVazios(),
                    'dias' => [],
                ];
            }

            if (!isset($relatorio['equipamentos'][$equipamentoId]['dias'][$data])) {
                $relatorio['equipamentos'][$equipamentoId]['dias'][$data] = [
                    'data' => $data,
                    'totais' => $this->totaisVazios(),
                    'itens' => [],
                ];
            }

            $item = [
                'id' => (int) $registro['id'],
                'demanda' => $registro['demanda'],
                'produto_id' => $registro['produto_id'],
                'descricao' => trim(
                    ($registro['descricao_produto'] ?? '')
                    . ' '
                    . ($registro['descricao_complementar'] ?? '')
                ),
                'quantidade' => $this->numero($registro['quantidade']),
                'peso_estimado' => $this->numero($registro['peso_estimado']),
                'pecas_realizadas' => $this->numero($registro['pecas_realizadas']),
                'peso_realizado' => $this->numero($registro['peso_realizado']),
                'observacao' => $registro['observacao'],
                'ordem' => $registro['ordem'],
            ];

            $relatorio['equipamentos'][$equipamentoId]['dias'][$data]['itens'][] = $item;

            $this->adicionarTotais(
                $relatorio['totais'],
                $item
            );

            $this->adicionarTotais(
                $relatorio['equipamentos'][$equipamentoId]['totais'],
                $item
            );

            $this->adicionarTotais(
                $relatorio['equipamentos'][$equipamentoId]['dias'][$data]['totais'],
                $item
            );
        }

        // Converte os mapas associativos em listas, facilitando o consumo pela view.
        foreach ($relatorio['equipamentos'] as &$equipamento) {
            $equipamento['dias'] = array_values($equipamento['dias']);
        }
        unset($equipamento);

        $relatorio['equipamentos'] = array_values($relatorio['equipamentos']);

        return $relatorio;
    }

    private function totaisVazios(): array
    {
        return [
            'quantidade' => 0,
            'peso_estimado' => 0.0,
            'pecas_realizadas' => 0,
            'peso_realizado' => 0.0,
        ];
    }

    private function adicionarTotais(array &$totais, array $item): void
    {
        $totais['quantidade'] += $item['quantidade'];
        $totais['peso_estimado'] += $item['peso_estimado'];
        $totais['pecas_realizadas'] += $item['pecas_realizadas'];
        $totais['peso_realizado'] += $item['peso_realizado'];
    }

    private function numero(mixed $valor): float
    {
        if ($valor === null || $valor === '') {
            return 0.0;
        }

        return (float) str_replace(',', '.', (string) $valor);
    }
}
