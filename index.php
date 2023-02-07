<?php
require(__DIR__.'/vendor/autoload.php');

$reader = new PhpOffice\PhpSpreadsheet\Reader\Xlsx;
$load_file = $reader->load('translation.xlsx');

$A_to_Z = range('A', 'Z');
$translations = array();

for ($row = 1; $row < ($load_file->getActiveSheet()->getHighestRow() + 1); $row ++) {
    if ($row === 1) {
        continue;
    }

    for ($col = 0; $col < (array_search($load_file->getActiveSheet()->getHighestColumn(), $A_to_Z) + 1); $col++) {
        if ($row === 2 OR $col === 0) {
            if ($col > 0) {
                $LANG = $load_file->getActiveSheet()->getCell($A_to_Z[$col].$row)->getValue();
                $LANG = strtolower($LANG);
                $translations[$LANG] = array();
            }

            continue;
        }        

        $LANG = $load_file->getActiveSheet()->getCell($A_to_Z[$col].'2')->getValue();
        $KEY = $load_file->getActiveSheet()->getCell('A'.$row)->getValue();
        $VAL = $load_file->getActiveSheet()->getCell($A_to_Z[$col].$row)->getValue();

        $translations[$LANG][$KEY] = $VAL;
    }
}

if (!file_exists('language')) {
    mkdir('language');
}

foreach (array_keys($translations) as $language) {
    if (!file_exists('language'.DIRECTORY_SEPARATOR.$language)) {
        mkdir('language'.DIRECTORY_SEPARATOR.$language);
    }

    file_put_contents('language'.DIRECTORY_SEPARATOR.$language.DIRECTORY_SEPARATOR.'app.json', json_encode($translations[$language], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}