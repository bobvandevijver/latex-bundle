<?php

namespace Bobv\LatexBundle\Model;

class ParseError
{
  /**
   * @param string[]     $logLines
   * @param TexSnippet[] $texSnippets
   */
  public function __construct(
      public readonly array $logLines,
      public readonly array $texSnippets = [],
  ) {
  }

  public function getLogSource(): string {
    return implode("\n", $this->logLines);
  }
}
