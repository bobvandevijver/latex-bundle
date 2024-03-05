<?php

namespace Bobv\LatexBundle\Exception;

/**
 * Simple \Exception extend for better error origin check
 */
class LatexParseException extends LatexException
{
  const LOG_GET_LINES = 4;
  const LOG_MAX_LINES = 10;
  const TEX_GET_LINES = 8;
  const TEX_MAX_LINES = 20;

  protected array $filteredTexSource = [];

  protected array $filteredLogSource = [];

  public function __construct(string $texLocation, int $exitCode, ?array $pdfLatexOutput = null, ?string $exitCodeText = null)
  {
    if ($exitCodeText !== null) {
      $exitCodeText = sprintf(' (%s)', $exitCodeText);
    } else {
      $exitCodeText = '';
    }

    $message = 'Something went wrong during the execution of the pdflatex command, as it returned ' . $exitCode . $exitCodeText. '. See the log file (' . explode('.tex', $texLocation)[0] . '.log ) for all details.';

    $this->findErrors($pdfLatexOutput, $texLocation);

    parent::__construct($message, $exitCode);
  }

  /**
   * Return an extended error message together with the extracted errors
   */
  public function getExtendedMessage(): string
  {
    $message = $this->getMessage();

    if (count($this->filteredLogSource) > 0) {
      $message .= "\n\n\nBelow some more info is tried to extract from the error:\n";
      $message .= implode("\n", $this->filteredLogSource);
    }

    return $message;
  }

  /**
   * Try to find useful information on the error that has occurred
   * This is stored in the object properties $filteredLogSource and $filteredTexSource
   */
  protected function findErrors(array $errorOutput, ?string $texLocation = NULL): void
  {
    $refWarning       = strtolower('LaTeX Warning: Reference');
    $filteredErrors   = [];
    $filteredErrors[] = '---';

    array_walk($errorOutput, function ($value, $key) use (&$errorOutput, &$texLocation, &$filteredErrors, $refWarning) {

      // Find lines with an error
      if (preg_match_all('/error|missing|not found|undefined|too many|runaway|\$|you can\'t use|invalid/ui', $value) > 0) {
        if (!str_starts_with(strtolower($errorOutput[$key]), $refWarning)) {
          // Get the lines surrounding the error, but do not include empty lines

          // Get lines before the error
          $temp = [];
          for ($count = 0, $i = 0; $count < self::LOG_GET_LINES && $i < self::LOG_MAX_LINES; $i++) {
            if (isset($errorOutput[$key - $i])) {
              $value = trim(preg_replace('/\s+/', ' ', $errorOutput[$key - $i]));
              if ($value != '') {
                $temp[] = $value;
                $count++;
              }
            } else {
              break;
            }
          }
          $filteredErrors = array_merge($filteredErrors, array_reverse($temp));

          // Get lines after the error
          for ($count = 0, $i = 1; $count < self::LOG_GET_LINES && $i < self::LOG_MAX_LINES; $i++) {
            if (isset($errorOutput[$key + $i])) {
              $value = trim(preg_replace('/\s+/', ' ', $errorOutput[$key + $i]));
              if ($value != '') {
                $filteredErrors[] = $value;
                $count++;
              }
            } else {
              break;
            }
          }

          $filteredErrors[] = '---';
        }
      }

    });

    // Save in object
    $this->filteredLogSource = $filteredErrors;

    // Try to find matching tex lines
    // Check if a line number can be found in the errors
    $this->filteredTexSource[] = '---';
    if ($texLocation !== NULL) {
      $lineNumber = [];
      $texFile    = new \SplFileObject($texLocation);
      foreach ($this->filteredLogSource as $logLine) {
        preg_match('/l\.(\d+)/ui', $logLine, $lineNumber);
        if (count($lineNumber) == 2) {

          // Get lines before the linenumber
          $temp = [];
          for ($count = 0, $i = 0; $count < self::TEX_GET_LINES && $i < self::TEX_MAX_LINES; $i++) {
            $texFile->seek($lineNumber[1] - $i);
            if ($texFile->valid()) {
              $value = trim(preg_replace('/\s+/', ' ', $texFile->current()));
              if ($value != '') {
                $temp[] = $value;
                $count++;
              }
            } else {
              break;
            }
          }
          $this->filteredTexSource = array_merge($this->filteredTexSource, [$lineNumber[0]], array_reverse($temp));

          // Get lines after the line number
          for ($count = 0, $i = 1; $count < self::TEX_GET_LINES && $i < self::TEX_MAX_LINES; $i++) {
            $texFile->seek($lineNumber[1] + $i);
            if ($texFile->valid()) {
              $value = trim(preg_replace('/\s+/', ' ', $texFile->current()));
              if ($value != '') {
                $this->filteredTexSource[] = $value;
                $count++;
              }
            } else {
              break;
            }
          }
          $this->filteredTexSource[] = '---';
        }
      }
    }
  }

  public function getFilteredTexSource(): string
  {
    return implode("\n", $this->filteredTexSource);
  }

  public function getFilteredLogSource(): string
  {
    return implode("\n", $this->filteredLogSource);
  }
} 
