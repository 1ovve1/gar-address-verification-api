<?php

declare(strict_types=1);

namespace CLI\XMLParser\Reader;

use CLI\XMLParser\Files\XMLFile;

interface ReaderVisitor
{
    public function read(XMLFile $file): void;
}
