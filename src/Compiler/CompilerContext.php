<?php

namespace Bobv\LatexBundle\Compiler;

/** Compilation context, meant to hold information about the current LaTeX compilation job. */
final class CompilerContext
{
  /** @var mixed To be used by the compiler to store additional context when necessary. */
  public mixed $compilerContext = null;

  /**
   * @param string   $workingDirectory the working directory where the LaTeX files are located
   * @param string   $baseName         the base name of the LaTeX file to compile, for example `document`
   * @param string[] $pdflatexArgs     the arguments that should be passed to pdflatex
   */
  public function __construct(
    public readonly string $workingDirectory,
    public readonly string $baseName,
    public readonly array $pdflatexArgs,
  ) {
  }
}
