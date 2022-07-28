<?php

declare(strict_types=1);

namespace CLI\XMLReader\Files;

use CLI\XMLReader\Reader\ReaderVisitor;

interface FileCollection
{
    /**
     * @param ReaderVisitor $reader
     * @param String[] $options
     * @return void
     */
    public function exec(ReaderVisitor $reader, array $options = []): void;
}
