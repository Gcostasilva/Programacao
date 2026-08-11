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

    $pdo->exec("DELETE FROM tb_previsao");
    $pdo->exec("ALTER TABLE tb_previsao AUTO_INCREMENT = 1");


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
    $stmt = $pdo->prepare("INSERT INTO tb_previsao (codigo, armazem, quantidade) VALUES (:codigo, :armazem, :quantidade)");

    $linhasImportadas = 0;

    // Lê linha por linha até o final do arquivo
    while (($dados = fgetcsv($handle, 1000, ';')) !== false) {
        // Mapeia os dados do CSV para os parâmetros da query
        $stmt->execute([
            ':codigo' => trim($dados[0]),
            ':armazem' => trim($dados[1]),
            ':quantidade' => trim($dados[2]),
        ]);
        $linhasImportadas++;
    }
    ;

    // Fecha o arquivo
    fclose($handle);

    return "Importação concluída! Total de $linhasImportadas linhas inseridas.";
}

?>