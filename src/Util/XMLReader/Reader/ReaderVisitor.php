<?php

declare(strict_types=1);

namespace GAR\Util\XMLReader\Reader;

use GAR\Util\XMLReader\Files\XMLFile;

interface ReaderVisitor
{
    public function read(XMLFile $file): void;
}
