<?php

namespace GzipUnzip;

use Exception;
use GzipUnzip\Archive\Archive;
use GzipUnzip\Archive\RarArchive;
use GzipUnzip\Archive\ZipArchive;

class GzipUnzip
{
    private $file;

    /**
     * @var Archive $archive
     */
    private $archive;

    private $extractDir;

    private $extractFile;

    /**
     * @param $file
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * @throws Exception
     */
    public function open($file = null): bool
    {
        !$file ?: $this->file = $file;
        $this->adapter();
        return $this->archive->open();
    }

    /**
     * @throws Exception
     */
    public function acquire(string $tempDir): array
    {
        $this->extractDir = $tempDir;
        if (!$this->archive) {
            $this->adapter();
        }
        return $this->archive->acquire($tempDir);
    }

    /**
     * @throws Exception
     */
    public function extract(string $tempDir, $tempFile = null): bool
    {
        $this->extractDir = $tempDir;
        $this->extractFile = $tempFile;
        if (!$this->archive) {
            $this->adapter();
        }
        return $this->archive->extract($this->extractDir, $this->extractFile);
    }


    /**
     * @throws Exception
     */
    private function adapter()
    {
        if (!file_exists($this->file)) {
            throw new Exception($this->file . ' is no exist');
        }
        $fileInfo = pathinfo($this->file);

        switch ($fileInfo['extension']) {
            case 'zip':
                $this->archive = new ZipArchive($this->file);
                break;
            case 'rar':
                $this->archive = new RarArchive($this->file);
                break;
            default:
                throw new Exception('no allowed format ' . $fileInfo['extension']);

        }
    }


//    public function
}