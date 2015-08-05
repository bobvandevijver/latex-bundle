<?php

namespace BobV\LatexBundle\Exception;

/**
 * Class LatexException
 * Simple \Exception extend for better error origin check
 */
class LatexParseException extends LatexException
{


  const LOG_GET_LINES = 4;
  const LOG_MAX_LINES = 10;
  const TEX_GET_LINES = 8;
  const TEX_MAX_LINES = 20;

  protected $filteredTexSource = array();

  protected $filteredLogSource = array();

  /**
   * @param string     $texLocation
   * @param int        $exitCode
   * @param array|NULL $pdfLatexOutput
   */
  public function __construct($texLocation, $exitCode, array $pdfLatexOutput = NULL, $exitCodeText = NULL)
  {
    if($exitCodeText !== NULL){
      $exitCodeText = sprintf(' (%s)', $exitCodeText);
    }else{
      $exitCodeText = '';
    }

    $message = 'Something went wrong during the execution of the pdflatex command, as it returned ' . $exitCode . $exitCodeText. '. See the log file (' . explode('.tex', $texLocation)[0] . '.log ) for all details.';

    $this->findErrors($pdfLatexOutput, $texLocation);

    parent::__construct($message, $exitCode);
  }

  /**
   * Return an extended error message together with the extracted errors
   *
   * @return string
   */
  public function getExtendedMessage()
  {
    $message = $this->getMessage();

    if (count($this->filteredLogSource) > 0) {
      $message .= "\n\n\nBelow some more info is tried to extract from the error:\n";
      $message .= implode("\n", $this->filteredLogSource);
    }

    return $message;
  }

  /**
   * Try to find usefull information on the error that has occurred
   *
   * @param array  $errorOutput
   * @param string $texLocation
   *
   * @return array Contains the filtered output which should only contain information about the errors
   */
  protected function findErrors(array $errorOutput, $texLocation = NULL)
  {
    $refWarning       = strtolower('LaTeX Warning: Reference');
    $filteredErrors   = array();
    $filteredErrors[] = "---";

    array_walk($errorOutput, function ($value, $key) use (&$errorOutput, &$texLocation, &$filteredErrors, $refWarning) {

      // Find lines with an error
      if (preg_match_all('/error|missing|not found|undefined|too many|runaway|\$|you can\'t use|invalid/ui', $value) > 0) {
        if (substr(strtolower($errorOutput[$key]), 0, strlen($refWarning)) !== $refWarning) {
          // Get the lines surrounding the error, but do not include empty lines

          // Get lines before the error
          $temp = array();
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

          $filteredErrors[] = "---";
        }
      }

    });

    // Save in object
    $this->filteredLogSource = $filteredErrors;

    // Try to find matching tex lines
    // Check if a line number can be found in the errors
    $this->filteredTexSource[] = "---";
    if ($texLocation !== NULL) {
      $lineNumber = array();
      $texFile    = new \SplFileObject($texLocation);
      foreach ($this->filteredLogSource as $logLine) {
        preg_match('/l\.(\d+)/ui', $logLine, $lineNumber);
        if (count($lineNumber) == 2) {

          // Get lines before the linenumber
          $temp = array();
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
          $this->filteredTexSource = array_merge($this->filteredTexSource, array($lineNumber[0]), array_reverse($temp));

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

          $this->filteredTexSource[] = "---";

        }
      }
    }
  }

  /**
   * @return string
   */
  public function getFilteredTexSource()
  {
    return implode("\n", $this->filteredTexSource);
  }

  /**
   * @return string
   */
  public function getFilteredLogSource()
  {
    return implode("\n", $this->filteredLogSource);
  }


} 
