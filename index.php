<?php
include 'vendor/autoload.php';

$parser = new \Smalot\PdfParser\Parser();

$pdf = $parser->parseFile('7842388475.pdf');

$text = $pdf->getText();

// echo $text;

// Регулярные выражения 
$licenses = '/Номер\s*лицензии\s*(\d{2}-\d{5}Ф|\d{4}|ЛСЗ\s*№\s*\d{7}\s*РЕГ.\s*№\s*\d{3}Н).*?Дата\s*начала\s*действия\s*лицензии\s*([\d]{2}\.[\d]{2}\.[\d]{4})\s*Вид\s*лицензируемой\s*деятельности,\s*на\s*который\s*выдана\s*лицензия\s*(.*?)\s*Наименование\s*лицензирующего\s*органа,\s*выдавшего\s*или\s*переоформившего\s*лицензию\s*(.*?)\s*ГРН\s*и/msu';

$registryLicenses = '/Номер\s*лицензии,\s*присвоенный\s*в\s*Едином\s*реестре\s*учета\s*лицензий\s*(Л\d{3}-\d{5}-\d{2}\/\d{8})?.*?Дата\s*начала\s*действия\s*лицензии\s*([\d]{2}\.[\d]{2}\.[\d]{4})?.*?Вид\s*лицензируемой\s*деятельности,\s*на\s*который\s*выдана\s*лицензия\s*(.*?)\s*Наименование\s*лицензирующего\s*органа,\s*выдавшего\s*или\s*переоформившего\s*лицензию\s*(.*?)\s*ГРН\s*и/msu';

$multipleLicenses = '/Номер\s*лицензии\s*(\d{2}-\d{5}Ф|\d{4}|ЛСЗ\s*№\s*\d{7}\s*РЕГ.\s*№\s*\d{3}Н)\s*Номер\s*лицензии,\s*присвоенный\s*в\s*Едином\s*реестре\s*учета\s*лицензий\s*(Л\d{3}-\d{5}-\d{2}\/\d{8})?.*?Дата\s*начала\s*действия\s*лицензии\s*([\d]{2}\.[\d]{2}\.[\d]{4})?.*?Вид\s*лицензируемой\s*деятельности,\s*на\s*который\s*выдана\s*лицензия\s*(.*?)\s*Наименование\s*лицензирующего\s*органа,\s*выдавшего\s*или\s*переоформившего\s*лицензию\s*(.*?)\s*ГРН\s*и/msu';

// Массивы для хранения номеров лицензий
$dataLicenses = [];
$registryLicensesData = [];
$multipleData = [];


// Поиск номеров лицензий с использованием регулярных выражений
preg_match_all($licenses, $text, $matchesLicenses, PREG_SET_ORDER);
preg_match_all($registryLicenses, $text, $matchesRegistryLicenses, PREG_SET_ORDER);
preg_match_all($multipleLicenses, $text, $matchesLicensesMultiple, PREG_SET_ORDER);

// echo "<pre>";
// print_r($matchesLicensesMultiple);
// echo "</pre>";

foreach ($matchesLicenses as $match) {

    $officialNum = $match[1];
    $issuerName = $match[4];
    $dateStart = $match[2];
    $activity = $match[3];


    $dataLicenses[$officialNum] = [
        'officialNum' => $officialNum,
        'issuerName' => $issuerName,
        'dateStart' => $dateStart,
        'activity' => $activity,

    ];
}

foreach ($matchesRegistryLicenses as $match) {

    $officialNum = $match[1];
    $issuerName = $match[4];
    $dateStart = $match[2];
    $activity = $match[3];


    $registryLicensesData[$officialNum] = [
        'officialNum' => $officialNum,
        'issuerName' => $issuerName,
        'dateStart' => $dateStart,
        'activity' => $activity,

    ];
}


foreach ($matchesLicensesMultiple as $match) {

    $officialNum = $match[1];
    $issuerName = $match[5];
    $dateStart = $match[3];
    $activity = $match[4];


    $multipleData[$officialNum] = [
        'officialNum' => $officialNum,
        'issuerName' => $issuerName,
        'dateStart' => $dateStart,
        'activity' => $activity,

    ];
}

foreach ($matchesLicensesMultiple as $match) {

    $officialNum = $match[2];
    $issuerName = $match[5];
    $dateStart = $match[3];
    $activity = $match[4];


    $multipleData[$officialNum] = [
        'officialNum' => $officialNum,
        'issuerName' => $issuerName,
        'dateStart' => $dateStart,
        'activity' => $activity,

    ];
}

foreach ($matchesRegistryLicenses as $match) {

    $officialNum = $match[1];
    $issuerName = $match[4];
    $dateStart = $match[2];
    $activity = $match[3];


    $multipleData[$officialNum] = [
        'officialNum' => $officialNum,
        'issuerName' => $issuerName,
        'dateStart' => $dateStart,
        'activity' => $activity,

    ];
}

$uniqueLicenses = array_merge(array_diff_key($dataLicenses, $multipleData), array_diff_key($multipleData, $dataLicenses));

// echo "<pre>";
// print_r($uniqueLicenses);
// echo "</pre>";

$result = json_encode(array_values($uniqueLicenses), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

file_put_contents('license_example.json', $result);
