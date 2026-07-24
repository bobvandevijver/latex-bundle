<?php

namespace Bobv\LatexBundle\Compiler;

abstract class AbstractTimeoutCompiler implements LatexCompilerInterface
{
  /** Default timeout from the Symfony Process component. */
  protected int|float|null $timeout = 60;

  public function setTimeout(float|int|null $timeout): LatexCompilerInterface
  {
    $this->timeout = $timeout;

    return $this;
  }
}
