<?php

namespace Bobv\LatexBundle\Exception;

use RuntimeException;

class TemplateNotSetException extends RuntimeException
{
  public function __construct()
  {
    parent::__construct('Template has not yet been set');
  }
}
