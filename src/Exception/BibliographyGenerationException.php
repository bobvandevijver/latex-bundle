<?php

namespace BobV\LatexBundle\Exception;

use Throwable;

class BibliographyGenerationException extends LatexException
{

  /**
   * @param string      $texLocation
   * @param int         $exitCode
   * @param string|null $exitCodeText
   */
  public function __construct($texLocation, $exitCode, $exitCodeText = NULL) {
    if ($exitCodeText !== NULL) {
      $exitCodeText = sprintf(' (%s)', $exitCodeText);
    } else {
      $exitCodeText = '';
    }

    $message = 'Something went wrong during the execution of the bibliography command, as it returned ' . $exitCode . $exitCodeText . '. See the log file (' . explode('.tex', $texLocation)[0] . '.log ) for all details.';

    parent::__construct($message, $exitCode);
  }
}
