<?php

namespace Bobv\LatexBundle\Model;

class TexSnippet
{
  /** @param TexLine[] $lines */
  public function __construct(
      public readonly int $lineNumber,
      public readonly array $lines,
  ) {
  }

  public function getTexSnippet(): string {
    return implode("\n", array_map(
        static fn (TexLine $l): string => $l->line,
        $this->lines,
    ));
  }
}
