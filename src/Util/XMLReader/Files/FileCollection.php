<?php

namespace GAR\Util\XMLReader\Files;

use GAR\Util\XMLReader\Reader\ReaderVisitor;

interface FileCollection
{
    public function exec(ReaderVisitor $reader): void;
}
