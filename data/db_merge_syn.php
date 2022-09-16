<?php
$dirList = ["procedure", "triggers", "function", "procedure/SYNERGY"];
$fileDataAll = "";

foreach ($dirList as $dir) {
    $scanned_directory = array_diff(scandir(__DIR__ . '/' . $dir), array('..', '.'));
    foreach ($scanned_directory as $filename) {
        $file = __DIR__ . '/' . $dir . "/" . $filename;
        if (is_file($file)) {
            $fileDataAll .= file_get_contents($file) . "/
            ";
        }
    }
}

$my_file = 'database/fns_trgs_procs.sql';
$handle = fopen(__DIR__ . '/' . $my_file, 'w') or die('Cannot open file:  ' . $my_file);
fwrite($handle, $fileDataAll);

