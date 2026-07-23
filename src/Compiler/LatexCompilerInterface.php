<?php

namespace Bobv\LatexBundle\Compiler;

interface LatexCompilerInterface
{
  /**
   * Run a pdflatex process.
   *
   * @return LatexCompilerResult The process output
   */
  public function pdflatex(
    string $workingDirectory,
    string $commandArguments,
  ): LatexCompilerResult;

  /**
   * Run a bibtex process.
   *
   * @return LatexCompilerResult The process output
   */
  public function bibtex(
    string $workingDirectory,
    string $commandArguments,
  ): LatexCompilerResult;

  /** bibtex support is optional and needs to be configured. */
  public function supportsBibliography(): bool;

  /**
   * Sets the process timeout (max. runtime).
   * To disable the timeout, set this value to null.
   *
   * @param int|float|null $timeout The timeout in seconds
   */
  public function setTimeout(int|float|null $timeout): self;
}
