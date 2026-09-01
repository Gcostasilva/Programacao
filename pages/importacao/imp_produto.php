<?php

require_once 'C:\xampp\htdocs\Programacao\config\banco.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_FILES['arquivo']['error'] == 0) {

        limparBanco($pdo);
        echo importarCsvParaBanco($_FILES['arquivo']['tmp_name'], $pdo);

    } else {

        echo "Erro ao enviar o arquivo.";

    }

}
function limparBanco($pdo) {

    $pdo->beginTransaction();

    $pdo->exec("DELETE FROM produtos");
    $pdo->exec("ALTER TABLE produtos AUTO_INCREMENT = 1");


};
function importarCsvParaBanco($caminhoArquivo, $pdo)
{
    // Abre o arquivo CSV para leitura
    $handle = fopen($caminhoArquivo, 'r');

    if ($handle === false) {
        return "Erro ao abrir o arquivo CSV.";
    }

    // Ignora a primeira linha (cabeçalho) se houver
    fgetcsv($handle, 1000, ';');

    // Prepara a query SQL (ajuste os campos e a tabela conforme o seu banco)
    $stmt = $pdo->prepare("INSERT INTO produtos (grupo, codigo, descricao, espessura, peso_liquido, especial) VALUES (:grupo, :codigo, :descricao, :espessura, :peso_liquido, :especial)");

    $linhasImportadas = 0;

    // Lê linha por linha até o final do arquivo
    while (($dados = fgetcsv($handle, 1000, ';')) !== false) {
        // Mapeia os dados do CSV para os parâmetros da query
        $stmt->execute([
            ':grupo' => trim($dados[0]),
            ':codigo' => trim($dados[1]),
            ':descricao' => trim($dados[2]),
            ':espessura' => trim(str_replace(',', '.', $dados[3])),
            ':peso_liquido' => trim(str_replace(',', '.', $dados[4])),
            ':especial' => trim($dados[5])
        ]);
        $linhasImportadas++;
    }
    ;

    // Fecha o arquivo
    fclose($handle);

    return "Importação concluída! Total de $linhasImportadas linhas inseridas.";
}

?>