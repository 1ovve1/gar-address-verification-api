<?php

declare(strict_types=1);

namespace CLI\XMLParser\Files;

use CLI\XMLParser\Reader\ReaderVisitor;

interface FileCollection
{
    /**
     * @param ReaderVisitor $reader
     * @param String[] $options
     * @return void
     */
    public function exec(ReaderVisitor $reader, array $options = []): void;
}
