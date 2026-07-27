<?php

namespace Bobv\LatexBundle\Compiler;

/** Compilation context, meant to hold information about the current LaTeX compilation job. */
final class CompilerContext
{
  /** @var mixed To be used by the compiler to store additional context when necessary. */
  public mixed $compilerContext = null;

  /** @param string[] $pdflatexArgs */
  public function __construct(
    public readonly string $baseName,
    public readonly string $workingDirectory,
    public readonly array $pdflatexArgs,
  ) {
  }
}
