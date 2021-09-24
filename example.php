<?php
require 'vendor/autoload.php';

$file = './12.rar';

$file = './33.zip';

$zip = new \GzipUnzip\GzipUnzip($file);

try {
    $re = $zip->acquire('./unzip');
    var_dump($re);exit;


    $tempFile = 'tempDir';
    $re = $zip->extract('./unzip',$tempFile);
    var_dump($re);


} catch (Exception $e) {
    var_dump($e->getMessage());
}

