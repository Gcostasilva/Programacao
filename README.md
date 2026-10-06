# Sistema de Programação de PCP

Sistema web desenvolvido em PHP para apoio ao planejamento e controle da produção (PCP), com foco em programação diária, semanal e quinzenal, controle de demanda, pedidos da indústria, estornos e geração de relatórios operacionais.

## Visão geral

Este projeto é uma aplicação interna para gestão da produção e da programação de fabricação. Ele reúne as principais informações de operação em uma interface web responsiva, permitindo registrar e acompanhar a produção por equipamento, produto, pedido e data.

As telas e módulos do sistema abrangem:

- Programação Diária
- Programação Semanal
- Programação Quinzenal
- Demanda
- Pedidos na Indústria
- Cadastros de apoio
- Relatórios e exportação

## Funcionalidades principais

### 1. Programação Diária
- Cadastro e acompanhamento da produção por equipamento e data
- Ordenação e reordenação dos itens
- Baixa de produção/execução
- Consulta por pedido, recurso, data e filtros
- Indicadores de peso programado, realizado e saldo

### 2. Programação Semanal
- Organização da produção por semana
- Cadastro de itens por produto, demanda e equipamento
- Reordenação de planilhas e busca por código
- Filtros e indicadores de desempenho

### 3. Programação Quinzenal
- Planejamento por período quinzenal
- Registro de itens com quantidade, peso estimado e produção realizada
- Estrutura de relatório para acompanhamento do período

### 4. Demanda
- Acompanhamento dos itens em demanda
- Visualização de estoque e pendências por código e armazém

### 5. Pedidos na Indústria
- Registro de pedidos
- Consulta de entradas e comentários
- Controle de estornos e motivos de estorno
- Integração com relatórios e view de acompanhamento

### 6. Cadastros
- Tipos de aço
- Vendedores
- Equipamentos / máquinas
- Motivos de estorno
- Importação de dados e cadastro de apoio

### 7. Relatórios
- Relatório de programação diária (RP 09)
- Relatório de programação semanal
- Relatório de programação quinzenal (RP 05)
- Relatório de estornos
- Exportação para Excel

## Tecnologias utilizadas

- PHP 8
- MySQL / MariaDB
- PDO para acesso ao banco
- HTML5 / CSS3 / JavaScript
- Bootstrap 5
- AdminLTE
- jQuery
- DataTables
- SortableJS

## Estrutura do projeto

```text
Programacao/
├── assets/                 # CSS, JS e assets visuais
├── config/                 # configuração do sistema e rotas
├── controllers/            # controladores do MVC
├── database/               # scripts SQL e dados auxiliares
├── includes/               # templates comuns: head, navbar, sidebar, footer
├── logs/                   # arquivos de log
├── models/                 # modelos de acesso ao banco
├── pages/                  # telas e módulos de negócio
├── services/               # serviços e lógica de relatórios
├── sql/                    # scripts SQL adicionais
├── uploads/                # arquivos enviados e imagens
├── index.php               # ponto de entrada da aplicação
├── producao_app.sql        # dump principal do banco
├── README.md               # documentação do projeto
├── favicon.ico             # ícone do sistema
└── ...
```

## Requisitos

Para executar o projeto localmente, são necessários:

- PHP 8.x
- Apache ou servidor web compatível com PHP
- MySQL 8 / MariaDB
- XAMPP, WAMP ou ambiente semelhante

## Instalação e execução

1. Clone ou copie o projeto para a pasta do seu servidor local, por exemplo:

```bash
C:\xampp\htdocs\Programacao
```

2. Crie o banco de dados `producao_app` no MySQL.

3. Importe o dump principal localizado em:

```text
producao_app.sql
```

4. Verifique as credenciais do banco em `config/banco.php`:

```php
$host = "localhost";
$db = "producao_app";
$user = "root";
$pass = "";
```

Se necessário, ajuste para o usuário/senha do seu ambiente.

5. Acesse a aplicação no navegador:

```text
http://localhost/Programacao/index.php?page=dashboard
```

## Configuração do banco

O sistema utiliza um arquivo de conexão em `config/banco.php` e o acesso é feito com PDO. A estrutura principal do banco contempla tabelas como:

- demanda
- programacao
- programacao_quinzenal
- pedidos_industria
- estornos
- tipos_aco
- vendedores
- maquinas
- motivos_estorno
- semanas
- tb_previsao

Os scripts SQL em `database/` e `sql/` ajudam a manter essa estrutura e dados auxiliares do sistema.

## Fluxo de uso típico

1. Cadastre ou importe os dados de apoio (aço, vendedores, máquinas, motivos de estorno).
2. Registre a demanda de produção.
3. Planeje a produção por programação diária, semanal ou quinzenal.
4. Acompanhe pedidos da indústria e estornos.
5. Emita relatórios para controle e acompanhamento operacional.

## Observações

- O projeto foi estruturado como uma aplicação web de negócios legada, com foco em produtividade operacional do setor industrial.
- A interface utiliza o padrão de template de administração do AdminLTE, com navegação em sidebar e páginas dinâmicas baseadas em `?page=`.
- A aplicação é orientada a rotas customizadas por meio de `config/rotas.php` e `config/router.php`.

## Licença

Este projeto não informa uma licença específica no repositório. Verifique com o responsável do projeto antes de redistribuir ou reutilizar em outros ambientes.

## Autor e manutenção

Este README foi escrito para documentar a estrutura e os usos principais do sistema conforme a análise do código-fonte disponível no repositório. Se for necessário, futuras melhorias podem incluir:

- documentação detalhada por módulo
- guia de deploy em produção
- diagrama de banco de dados
- padronização de nomenclatura e arquitetura
- testes automatizados

