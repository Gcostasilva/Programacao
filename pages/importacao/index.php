<div class="container">
    <br><br>
    <div class="card-header">
        <h1 style="display:inline-block;">Importação de dados via CSV</h1>
        <p>Selecione o arquivo correspondente a cada etapa e clique em Importar.</p>
    </div>

    <br><br>




    <!-- Card 1: Produtos -->
    <div class="card" style="display: grid; grid-template-columns: 1fr 1fr; box-shadow: rgba(50, 50, 93, 0.25) 0px 50px 100px -20px, rgba(0, 0, 0, 0.3) 0px 30px 60px -30px, rgba(10, 37, 64, 0.35) 0px -2px 6px 0px inset;">
        <div class="card-header">
            <h1>Importar Produtos</h1>
            Grupo, Código, Descrição, Especial.
        </div>
        <form action="pages/importacao/imp_produto.php" method="post" enctype="multipart/form-data">
            <div class="text-info-emphasis">Somente arquivos .csv</div>
            <div class="text-body-emphasis" onclick="this.querySelector('input').click()">
                <input type="file" class="btn-primary form-control" name="arquivo" accept=".csv" required
                    onchange="mostrarNome(this, 'nome-1')">
            </div>
            <button type="submit" class="btn-primary form-control">⬆ Importar pedidos</button>
        </form>
    </div>
    <br>
    <!-- Card 2: Demanda -->
    <div class="card" style="display: grid; grid-template-columns: 1fr 1fr;box-shadow: rgba(50, 50, 93, 0.25) 0px 50px 100px -20px, rgba(0, 0, 0, 0.3) 0px 30px 60px -30px, rgba(10, 37, 64, 0.35) 0px -2px 6px 0px inset;">
        <div class="card-header">
            <h1>Importar Demanda</h1>
            Grupo, Código, Armazem, Estoque, Pedencia.
        </div>

        <form action="pages/importacao/imp_demanda.php" method="post" enctype="multipart/form-data">
            <div class="text-info-emphasis">Somente arquivos .csv</div>
            <div class="text-body-emphasis" onclick="this.querySelector('input').click()">
                <input type="file" class="btn-primary form-control" name="arquivo" accept=".csv" required
                    onchange="mostrarNome(this, 'nome-1')">
            </div>
            <button type="submit" class="btn-primary form-control">⬆ Importar demanda</button>
        </form>
    </div>
    <br>
    <!-- Card 3: Previsão -->
    <div class="card" style="display: grid; grid-template-columns: 1fr 1fr;box-shadow: rgba(50, 50, 93, 0.25) 0px 50px 100px -20px, rgba(0, 0, 0, 0.3) 0px 30px 60px -30px, rgba(10, 37, 64, 0.35) 0px -2px 6px 0px inset;">
        <div class="card-header">
            <h1>Importar Previsão</h1>
            Código, Armazem, Quantidade.
        </div>

        <form action="pages/importacao/imp_previsao.php" method="post" enctype="multipart/form-data">
            <div class="text-info-emphasis">Somente arquivos .csv</div>
            <div class="text-body-emphasis" onclick="this.querySelector('input').click()">
                <input type="file" class="btn-primary form-control" name="arquivo" accept=".csv" required
                    onchange="mostrarNome(this, 'nome-1')">
            </div>
            <button type="submit" class="btn-primary form-control">⬆ Importar previsão</button>
        </form>
    </div>
    <br>

    <div class="dica">
        ⚠ <b>Atenção:</b> importe sempre na ordem — primeiro os <b>Produtos</b>,
        depois a <b>Demanda</b>.
        Importar fora de ordem pode causar erros de vínculo entre os registros.
    </div>
</div>

</div>