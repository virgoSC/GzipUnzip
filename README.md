# GzipUnzip

PHP压缩解压
支持格式：zip、rar

```phpregexp

$zip = new \GzipUnzip\GzipUnzip($file);

try {
    //获取解压内容 MD5分布储存
    $re = $zip->acquire('./unzip');
    $exampleRe = [
        '文件名1'=>'随机地址1',
        '文件名2'=>'随机地址2',
    ]
    var_dump($re);exit;

    

    //解压到一个文件
    $tempFile = 'tempDir'; //解压地址
    $re = $zip->extract('./unzip',$tempFile) : bool;
    var_dump($re);


} catch (Exception $e) {
    var_dump($e->getMessage());
}

```

