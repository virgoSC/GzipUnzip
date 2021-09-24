<?php

namespace GzipUnzip\Archive;

use Exception;

class RarArchive implements Archive
{
    /**
     * @var \RarArchive $archive
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

        if (!class_exists(\RarArchive::class)) {
            throw new Exception('not found RarArchive php expansion');
        }

        $this->file = $file;
    }


    /**
     * @throws Exception
     */
    public function open($file = null): bool
    {
        !$file ?: $this->file = $file;

        $this->archive = \RarArchive::open($this->file);

        if ($this->archive === false) {
            throw new Exception($this->file . ': Failed to decompress the file');
        }

        $this->check = true;
        return $this->archive instanceof \RarArchive;
    }

    /**
     * @throws Exception
     */
    public function acquire(string $dir): array
    {
        if (!$this->check) {
            $this->open();
        }

        if (!$entries = $this->archive->getEntries()) {
            throw new Exception($this->file . ': Failed to fetching entries');
        }
        if (!count($entries)) {
            $this->archive->close();
            return [];
        }
        $set = [];
        foreach ($entries as $entry) {
            if ($entry->isDirectory()) {
                continue;
            }
            $fileName = $entry->getName();
            $fileTmpName = md5(uniqid()) . substr($fileName, strripos($fileName, '.'));
            $fileTmpDir = $dir . DIRECTORY_SEPARATOR . substr($fileTmpName, 0, 2) . DIRECTORY_SEPARATOR . substr($fileTmpName, 2, 2);
            if (!is_dir($fileTmpDir)) {
                mkdir($fileTmpDir, 0755, true);
            }

            $entry->extract($fileTmpDir, $fileTmpDir . DIRECTORY_SEPARATOR . $fileTmpName);
            $set[$fileName] = $fileTmpDir . DIRECTORY_SEPARATOR . $fileTmpName;
        }
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
        $entries = $this->archive->getEntries();


        foreach ($entries as $entry) {
            if ($entry->isDirectory()) {
                continue;
            }
            $entry->extract($dir);
        }
        return true;
    }
}