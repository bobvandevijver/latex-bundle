<?php

namespace Bobv\LatexBundle\Exception;

use Throwable;

class BibliographyGenerationException extends LatexException
{
  public function __construct(string $texLocation, int $exitCode, ?string $exitCodeText = null) {
    if ($exitCodeText !== null) {
      $exitCodeText = sprintf(' (%s)', $exitCodeText);
    } else {
      $exitCodeText = '';
    }

    $message = 'Something went wrong during the execution of the bibliography command, as it returned ' . $exitCode . $exitCodeText . '. See the log file (' . explode('.tex', $texLocation)[0] . '.log ) for all details.';

    parent::__construct($message, $exitCode);
  }
}
