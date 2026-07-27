<?php

namespace Bobv\LatexBundle\Compiler;

use Bobv\LatexBundle\Exception\LatexException;

interface LatexCompilerInterface
{
  /**
   * Run a pdflatex process.
   *
   * @return LatexCompilerResult The process output
   *
   * @throws LatexException
   */
  public function pdflatex(CompilerContext $context): LatexCompilerResult;

  /**
   * Run a bibtex process.
   *
   * @return LatexCompilerResult The process output
   *
   * @throws LatexException
   */
  public function bibtex(CompilerContext $context): LatexCompilerResult;

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
