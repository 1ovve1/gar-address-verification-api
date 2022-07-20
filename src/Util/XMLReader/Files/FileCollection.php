<?php

namespace GAR\Util\XMLReader\Files;

use GAR\Util\XMLReader\Reader\ReaderVisitor;

interface FileCollection
{
  function exec(ReaderVisitor $reader) : void;
}