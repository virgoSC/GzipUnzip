<?php

namespace GzipUnzip\Archive;

interface Archive
{

    public function open($file = null): bool;

    public function acquire(string $dir): array;

    public function extract(string $dir, $file = null): bool;
}