<?php

declare(strict_types=1);

namespace CLI\XMLReader\Reader;

use CLI\XMLReader\Files\XMLFile;

interface ReaderVisitor
{
    public function read(XMLFile $file): void;
}
