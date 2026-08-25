<div class="container row align-self-lg-auto mb-2 mt-0">
    <div class="col-md-2">
        <label class="form-label">Semana</label>
        <button type="button" onclick="adjustWeek(-1)"><i class="bi bi-dash"></i></button>
        <button type="button" onclick="adjustWeek(1)"><i class="bi bi-plus"></i></button>
        <input type="week" class="form-control" id="semana_filtro" name="semana" value="<?php $semanaAtual = date('o-\WW');
        echo $semanaAtual; ?>" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Recurso</label>
        <select class="form-select" name="recurso_filtro" id="recurso_filtro" required>
            <option value="" disabled selected>Selecione...</option>
            <?php
            foreach ($dados['recursos_semanal'] as $linha) {
                $id = htmlspecialchars($linha['id']);
                $nome = htmlspecialchars($linha['descricao']);
                echo "<option value=\"$id\">$nome</option>";
            }
            ?>
        </select>
    </div>
</div>

<?php
$dias = [
    1 => ['nome' => 'Segunda-feira', 'id' => 'segunda'],
    2 => ['nome' => 'Terça-feira', 'id' => 'terca'],
    3 => ['nome' => 'Quarta-feira', 'id' => 'quarta'],
    4 => ['nome' => 'Quinta-feira', 'id' => 'quinta'],
    5 => ['nome' => 'Sexta-feira', 'id' => 'sexta'],
];

$semana = isset($_POST['semana']) ? $_POST['semana'] : date('o-\WW');
$hoje = date('Y-m-d');
?>

<div id="areaSemanal">
    <!-- para alterar a tabela em si vá no arquivo dias.php -->
    <?php include __DIR__ . '../../dias.php'; ?>
</div>

