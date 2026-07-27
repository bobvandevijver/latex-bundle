<?php

namespace Bobv\LatexBundle\Compiler;

use Bobv\LatexBundle\Exception\LatexException;

interface LatexCompilerInterface
{
  /**
   * Run a pdflatex process.
   *
   * @throws LatexException
   *
   * @return LatexCompilerResult The process output
   */
  public function pdflatex(CompilerContext $context): LatexCompilerResult;

  /**
   * Run a bibtex process.
   *
   * @throws LatexException
   *
   * @return LatexCompilerResult The process output
   */
  public function bibtex(CompilerContext $context): LatexCompilerResult;

  /** bibtex support is optional and needs to be configured. */
  public function supportsBibliography(): bool;
}
