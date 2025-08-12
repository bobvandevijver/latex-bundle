<?php

namespace Bobv\LatexBundle\Model;

class TexLine
{
  public function __construct(
      public readonly int $lineNumber,
      public readonly string $line,
  ) {
  }
}
