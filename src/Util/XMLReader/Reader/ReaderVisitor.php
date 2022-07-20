<?php

namespace GAR\Util\XMLReader\Reader;

use GAR\Util\XMLReader\Files\XMLFile;

interface ReaderVisitor
{
  function read(XMLFile $file): void;
}