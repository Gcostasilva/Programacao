<?php
declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Método não permitido.');
}

if (!class_exists('ZipArchive')) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    exit('A extensão ZipArchive do PHP não está habilitada no servidor. Habilite a extensão zip no XAMPP para exportar arquivos XLSX.');
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload) || !isset($payload['cabecalhos'], $payload['linhas']) || !is_array($payload['cabecalhos']) || !is_array($payload['linhas'])) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Dados inválidos para exportação.');
}

$cabecalhos = array_slice(array_map(static fn($v) => trim((string)$v), $payload['cabecalhos']), 0, 11);
$linhas = array_slice($payload['linhas'], 0, 20000);
if (!$cabecalhos || !$linhas) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Não há registros para exportar.');
}

$colunas = count($cabecalhos);
$linhas = array_values(array_filter($linhas, static fn($linha) => is_array($linha)));
$agora = date('d/m/Y H:i');

function xlsxColuna(int $numero): string {
    $letras = '';
    while ($numero > 0) {
        $resto = ($numero - 1) % 26;
        $letras = chr(65 + $resto) . $letras;
        $numero = intdiv($numero - 1, 26);
    }
    return $letras;
}

function xlsxEscapar(string $valor): string {
    return htmlspecialchars($valor, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

function xlsxNumero($valor): ?float {
    $texto = trim((string)$valor);
    if ($texto === '') return null;
    $texto = preg_replace('/\s+/', '', $texto);
    if (str_contains($texto, ',') && str_contains($texto, '.')) {
        $texto = str_replace('.', '', $texto);
        $texto = str_replace(',', '.', $texto);
    } elseif (str_contains($texto, ',')) {
        $texto = str_replace(',', '.', $texto);
    }
    return is_numeric($texto) ? (float)$texto : null;
}

function xlsxCelula(int $linha, int $coluna, $valor, int $estilo, bool $numerica = false): string {
    $referencia = xlsxColuna($coluna) . $linha;
    if ($numerica && ($numero = xlsxNumero($valor)) !== null) {
        $textoNumero = rtrim(rtrim(number_format($numero, 6, '.', ''), '0'), '.');
        if ($textoNumero === '') $textoNumero = '0';
        return '<c r="' . $referencia . '" s="' . $estilo . '" t="n"><v>' . $textoNumero . '</v></c>';
    }
    $texto = (string)($valor ?? '');
    return '<c r="' . $referencia . '" s="' . $estilo . '" t="inlineStr"><is><t xml:space="preserve">' . xlsxEscapar($texto) . '</t></is></c>';
}

$ultimaColuna = xlsxColuna($colunas);
$ultimaLinha = 4 + count($linhas);
$sheetRows = [];
$sheetRows[] = '<row r="1" ht="28" customHeight="1">' . xlsxCelula(1, 1, 'RELATÓRIO DE DEMANDA', 1) . '</row>';
$sheetRows[] = '<row r="2" ht="20" customHeight="1">' . xlsxCelula(2, 1, 'Emitido em ' . $agora . ' | Total de registros: ' . count($linhas), 2) . '</row>';
$sheetRows[] = '<row r="3"/>';
$cellsHeader = '';
foreach ($cabecalhos as $i => $nome) $cellsHeader .= xlsxCelula(4, $i + 1, $nome, 3);
$sheetRows[] = '<row r="4" ht="30" customHeight="1">' . $cellsHeader . '</row>';

foreach ($linhas as $indice => $linha) {
    $numeroLinha = $indice + 5;
    $estiloBase = ($indice % 2 === 0) ? 4 : 5;
    $cells = '';
    for ($i = 0; $i < $colunas; $i++) {
        $valor = is_array($linha) ? ($linha[$i] ?? '') : '';
        $numerica = in_array($i, [4, 5, 6, 7, 8, 9, 10], true);
        $estilo = $numerica ? (($indice % 2 === 0) ? 6 : 7) : $estiloBase;
        $cells .= xlsxCelula($numeroLinha, $i + 1, $valor, $estilo, $numerica);
    }
    $sheetRows[] = '<row r="' . $numeroLinha . '">' . $cells . '</row>';
}

$larguras = [24, 16, 42, 10, 14, 14, 14, 14, 14, 16, 14];
$colsXml = '';
for ($i = 0; $i < $colunas; $i++) {
    $largura = $larguras[$i] ?? 18;
    $colsXml .= '<col min="' . ($i + 1) . '" max="' . ($i + 1) . '" width="' . $largura . '" customWidth="1"/>';
}

$sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
    . '<dimension ref="A1:' . $ultimaColuna . $ultimaLinha . '"/>'
    . '<sheetViews><sheetView workbookViewId="0"><pane ySplit="4" topLeftCell="A5" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
    . '<sheetFormatPr defaultRowHeight="18"/><cols>' . $colsXml . '</cols><sheetData>' . implode('', $sheetRows) . '</sheetData>'
    . '<autoFilter ref="A4:' . $ultimaColuna . $ultimaLinha . '"/>'
    . '<mergeCells count="2"><mergeCell ref="A1:' . $ultimaColuna . '1"/><mergeCell ref="A2:' . $ultimaColuna . '2"/></mergeCells>'
    . '<pageMargins left="0.25" right="0.25" top="0.5" bottom="0.5" header="0.2" footer="0.2"/>'
    . '<pageSetup orientation="landscape" fitToWidth="1" fitToHeight="0" paperSize="9"/>'
    . '</worksheet>';

$stylesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
    . '<numFmts count="1"><numFmt numFmtId="164" formatCode="#,##0.##"/></numFmts>'
    . '<fonts count="4">'
    . '<font><sz val="10"/><name val="Arial"/></font>'
    . '<font><b/><sz val="16"/><color rgb="FFFFFFFF"/><name val="Arial"/></font>'
    . '<font><i/><sz val="10"/><color rgb="FF555555"/><name val="Arial"/></font>'
    . '<font><b/><sz val="10"/><color rgb="FFFFFFFF"/><name val="Arial"/></font>'
    . '</fonts>'
    . '<fills count="4"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF244062"/><bgColor indexed="64"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFF0F4F8"/><bgColor indexed="64"/></patternFill></fill></fills>'
    . '<borders count="2"><border><left/><right/><top/><bottom/><diagonal/></border><border><left style="thin"><color rgb="FFD5DCE5"/></left><right style="thin"><color rgb="FFD5DCE5"/></right><top style="thin"><color rgb="FFD5DCE5"/></top><bottom style="thin"><color rgb="FFD5DCE5"/></bottom><diagonal/></border></borders>'
    . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
    . '<cellXfs count="8">'
    . '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>'
    . '<xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFill="1" applyFont="1" applyAlignment="1"><alignment vertical="center"/></xf>'
    . '<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment vertical="center"/></xf>'
    . '<xf numFmtId="0" fontId="3" fillId="2" borderId="1" xfId="0" applyFill="1" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
    . '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>'
    . '<xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>'
    . '<xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>'
    . '<xf numFmtId="164" fontId="0" fillId="3" borderId="1" xfId="0" applyNumberFormat="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>'
    . '</cellXfs><cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles></styleSheet>';

$tmp = tempnam(sys_get_temp_dir(), 'demanda_xlsx_');
$zip = new ZipArchive();
if ($zip->open($tmp, ZipArchive::OVERWRITE) !== true) {
    @unlink($tmp);
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Não foi possível montar o arquivo Excel.');
}

$zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
    . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
    . '<Default Extension="xml" ContentType="application/xml"/>'
    . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
    . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
    . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
    . '</Types>');
$zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
    . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
    . '</Relationships>');
$zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
    . '<sheets><sheet name="Demanda" sheetId="1" r:id="rId1"/></sheets></workbook>');
$zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
    . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
    . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
    . '</Relationships>');
$zip->addFromString('xl/styles.xml', $stylesXml);
$zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
$zip->close();

if (ob_get_length()) ob_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Demanda_' . date('Y-m-d') . '.xlsx"');
header('Content-Length: ' . filesize($tmp));
header('Cache-Control: no-store, no-cache, must-revalidate');
readfile($tmp);
@unlink($tmp);
exit;
