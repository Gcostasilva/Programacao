<?php

require_once __DIR__ . '/../models/RelatorioProgramacaoModel.php';

class RelatorioProgramacaoService
{
    private RelatorioProgramacaoModel $model;

    public function __construct(?RelatorioProgramacaoModel $model = null)
    {
        $this->model = $model ?? new RelatorioProgramacaoModel();
    }

    public function gerar(
        string $dataInicio,
        string $dataFim,
        ?int $recursoId = null
    ): array {
        $registros = $this->model->listarProgramacao($dataInicio, $dataFim, $recursoId);
        return $this->agruparProgramacao($registros, $dataInicio, $dataFim);
    }

    /**
     * Gera o RP 09 usando os mesmos campos/origem da tabela da Programação Diária.
     */
    public function gerarDiaria(string $data, ?int $recursoId = null): array
    {
        $registros = $this->model->listarProgramacaoDiaria($data, $recursoId);

        $relatorio = [
            'periodo' => ['inicio' => $data, 'fim' => $data],
            'totais' => ['peso' => 0.0, 'peso_realizado' => 0.0],
            'equipamentos' => [],
        ];

        foreach ($registros as $registro) {
            $equipamentoId = (int) $registro['recurso_id'];

            if (!isset($relatorio['equipamentos'][$equipamentoId])) {
                $relatorio['equipamentos'][$equipamentoId] = [
                    'id' => $equipamentoId,
                    'nome' => $registro['recurso'] ?: 'Equipamento não informado',
                    'totais' => ['peso' => 0.0, 'peso_realizado' => 0.0],
                    'itens' => [],
                ];
            }

            $item = [
                'id' => (int) $registro['id'],
                'pedido' => $registro['pedido'],
                'vendedor' => $registro['vendedor'],
                'espessura' => $this->numero($registro['espessura']),
                'aco' => $registro['aco'],
                'peso' => $this->numero($registro['peso']),
                'peso_realizado' => $this->numero($registro['peso_realizado']),
                'observacao' => $registro['observacao'],
                'falta_mp' => (int) $registro['falta_mp'],
                'ordem' => $registro['ordem'],
            ];

            $relatorio['equipamentos'][$equipamentoId]['itens'][] = $item;
            $relatorio['equipamentos'][$equipamentoId]['totais']['peso'] += $item['peso'];
            $relatorio['equipamentos'][$equipamentoId]['totais']['peso_realizado'] += $item['peso_realizado'];
            $relatorio['totais']['peso'] += $item['peso'];
            $relatorio['totais']['peso_realizado'] += $item['peso_realizado'];
        }

        $relatorio['equipamentos'] = array_values($relatorio['equipamentos']);
        return $relatorio;
    }

    /**
     * Gera o RP 05 diretamente da Programação Quinzenal.
     * A programação quinzenal não possui equipamento, portanto o resultado é um único documento.
     */
    public function gerarQuinzenal(string $quinzena): array
    {
        $registros = $this->model->listarProgramacaoQuinzenal($quinzena);

        $partes = explode('-', $quinzena);
        if (count($partes) !== 3 || !in_array($partes[2], ['1', '2'], true)) {
            throw new InvalidArgumentException('Quinzena inválida.');
        }

        $ano = (int) $partes[0];
        $mes = (int) $partes[1];
        $metade = (int) $partes[2];
        $inicio = sprintf('%04d-%02d-%02d', $ano, $mes, $metade === 1 ? 1 : 16);
        $fim = $metade === 1
            ? sprintf('%04d-%02d-15', $ano, $mes)
            : (new DateTime(sprintf('%04d-%02d-01', $ano, $mes)))->modify('last day of this month')->format('Y-m-d');

        $relatorio = [
            'quinzena' => $quinzena,
            'periodo' => ['inicio' => $inicio, 'fim' => $fim],
            'totais' => [
                'quantidade' => 0.0,
                'produzido' => 0.0,
                'saldo' => 0.0,
                'peso_estimado' => 0.0,
            ],
            'itens' => [],
        ];

        foreach ($registros as $registro) {
            $quantidade = $this->numero($registro['quantidade']);
            $produzido = $this->numero($registro['peca_realizada']);
            $saldo = $quantidade - $produzido;
            $pesoLiquido = $this->numero($registro['peso_liquido']);
            $pesoEstimado = $quantidade * $pesoLiquido;

            $item = [
                'id' => (int) $registro['id'],
                'produto_id' => $registro['produto_id'],
                'descricao' => $registro['descricao'],
                'quantidade' => $quantidade,
                'produzido' => $produzido,
                'saldo' => $saldo,
                'ordem_producao' => $registro['ordem_producao'],
                'peso_liquido' => $pesoLiquido,
                'peso_estimado' => $pesoEstimado,
                'observacao' => $registro['observacao'],
            ];

            $relatorio['itens'][] = $item;
            $relatorio['totais']['quantidade'] += $quantidade;
            $relatorio['totais']['produzido'] += $produzido;
            $relatorio['totais']['saldo'] += $saldo;
            $relatorio['totais']['peso_estimado'] += $pesoEstimado;
        }

        return $relatorio;
    }

    private function agruparProgramacao(array $registros, string $dataInicio, string $dataFim): array
    {
        $relatorio = [
            'periodo' => ['inicio' => $dataInicio, 'fim' => $dataFim],
            'totais' => ['quantidade' => 0.0, 'peso_estimado' => 0.0],
            'equipamentos' => [],
        ];

        foreach ($registros as $registro) {
            $equipamentoId = (int) $registro['recurso_id'];
            $data = (string) $registro['data'];

            if (!isset($relatorio['equipamentos'][$equipamentoId])) {
                $relatorio['equipamentos'][$equipamentoId] = [
                    'id' => $equipamentoId,
                    'nome' => $registro['recurso'] ?: 'Equipamento não informado',
                    'totais' => ['quantidade' => 0.0, 'peso_estimado' => 0.0],
                    'dias' => [],
                ];
            }

            if (!isset($relatorio['equipamentos'][$equipamentoId]['dias'][$data])) {
                $relatorio['equipamentos'][$equipamentoId]['dias'][$data] = [
                    'data' => $data,
                    'totais' => ['quantidade' => 0.0, 'peso_estimado' => 0.0],
                    'itens' => [],
                ];
            }

            $item = [
                'id' => (int) $registro['id'],
                'demanda' => $registro['demanda'],
                'produto_id' => $registro['produto_id'],
                'descricao' => trim(($registro['descricao_produto'] ?? '') . ' ' . ($registro['descricao_complementar'] ?? '')),
                'quantidade' => $this->numero($registro['quantidade']),
                'peso_estimado' => $this->numero($registro['peso_estimado']),
                'observacao' => $registro['observacao'],
                'ordem' => $registro['ordem'],
            ];

            $relatorio['equipamentos'][$equipamentoId]['dias'][$data]['itens'][] = $item;
            $relatorio['equipamentos'][$equipamentoId]['dias'][$data]['totais']['quantidade'] += $item['quantidade'];
            $relatorio['equipamentos'][$equipamentoId]['dias'][$data]['totais']['peso_estimado'] += $item['peso_estimado'];
            $relatorio['equipamentos'][$equipamentoId]['totais']['quantidade'] += $item['quantidade'];
            $relatorio['equipamentos'][$equipamentoId]['totais']['peso_estimado'] += $item['peso_estimado'];
            $relatorio['totais']['quantidade'] += $item['quantidade'];
            $relatorio['totais']['peso_estimado'] += $item['peso_estimado'];
        }

        foreach ($relatorio['equipamentos'] as &$equipamento) {
            $equipamento['dias'] = array_values($equipamento['dias']);
        }
        unset($equipamento);

        $relatorio['equipamentos'] = array_values($relatorio['equipamentos']);
        return $relatorio;
    }

    private function numero(mixed $valor): float
    {
        if ($valor === null || $valor === '') {
            return 0.0;
        }
        return (float) str_replace(',', '.', (string) $valor);
    }
}
