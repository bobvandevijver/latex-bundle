<?php

namespace BobV\LatexBundle\Exception;

use RuntimeException;

class LatexNotLockedException extends RuntimeException
{
  public function __construct()
  {
    parent::__construct('LaTeX lock not acquired, while it is required to do so! Did you call acquireLock before generation?');
  }
}
