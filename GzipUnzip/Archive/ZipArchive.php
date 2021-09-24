<?php

namespace GzipUnzip\Archive;

use Exception;

class ZipArchive implements Archive
{
    /**
     * @var \ZipArchive $archive
     */
    private $archive;

    private $file;

    private $check = false;

    /**
     * @param $file
     * @throws Exception
     */
    public function __construct($file)
    {
        $this->file = $file;

        if (!class_exists(\ZipArchive::class)) {
            throw new Exception('not found ZipArchive php expansion');
        }
        $this->archive = new \ZipArchive();

        $this->file = $file;
    }


    /**
     * @throws Exception
     */
    public function open($file = null): bool
    {
        !$file ?: $this->file = $file;

        if ($this->archive->open($this->file) !== true) {
            throw new Exception($this->file . ': Failed to decompress the file');
        }
        $this->check = true;
        return true;
    }

    /**
     * @throws Exception
     */
    public function acquire(string $dir): array
    {
        if (!$this->check) {
            $this->open();
        }
        $num = $this->archive->numFiles;

        if (!$num) {
            $this->archive->close();
            return [];
        }
        //修复中文
        for ($i = 0; $i < $num; $i++) {
            $item = $this->archive->statIndex($i, \ZipArchive::FL_ENC_RAW);
            $encode = mb_detect_encoding($item['name'], array("ASCII", 'UTF-8', "GB2312", "GBK", 'BIG5'));

            $name = mb_convert_encoding($item['name'], 'UTF-8', $encode);
            $this->archive->renameIndex($i, $name);
        }
        $this->archive->close();
        $this->archive->open($this->file);

        $set = [];

        for ($i = 0; $i < $num; $i++) {
            $fileName = $this->archive->getNameIndex($i);

            $fileTmpName = md5(uniqid()) . substr($fileName, strripos($fileName, '.'));
            $fileTmpDir = $dir . DIRECTORY_SEPARATOR . substr($fileTmpName, 0, 2) . DIRECTORY_SEPARATOR . substr($fileTmpName, 2, 2);
            if (!is_dir($fileTmpDir)) {
                mkdir($fileTmpDir, 0755, true);
            }

            @copy('zip://' . $this->file . '#' . $fileName, $fileTmpDir . DIRECTORY_SEPARATOR . $fileTmpName);

            $set[$fileName] = $fileTmpDir . DIRECTORY_SEPARATOR . $fileTmpName;

        }
        $this->archive->close();
        return $set;
    }

    /**
     * @throws Exception
     */
    public function extract(string $dir, $file = null): bool
    {
        if (!$this->check) {
            $this->open();
        }
        !$file ?: $dir .= DIRECTORY_SEPARATOR . $file;

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $num = $this->archive->numFiles;

        if (!$num) {
            $this->archive->close();
            return true;
        }

        //修复中文
        for ($i = 0; $i < $num; $i++) {
            $item = $this->archive->statIndex($i, \ZipArchive::FL_ENC_RAW);
            $encode = mb_detect_encoding($item['name'], array("ASCII", 'UTF-8', "GB2312", "GBK", 'BIG5'));

            $name = mb_convert_encoding($item['name'], 'UTF-8', $encode);
            $this->archive->renameIndex($i, $name);
        }
        $this->archive->close();
        $this->archive->open($this->file);

        $result = $this->archive->extractTo($dir);
        $this->archive->close();
        return $result;
    }
}