<?php

namespace Bobv\LatexBundle\Exception;

use Bobv\LatexBundle\Model\ParseError;
use Bobv\LatexBundle\Model\TexLine;
use Bobv\LatexBundle\Model\TexSnippet;
use SplFileObject;

/** Simple \Exception extend for better error origin check */
class LatexParseException extends LatexException
{
  const LOG_GET_LINES = 4;
  const LOG_MAX_LINES = 10;
  const TEX_GET_LINES = 8;
  const TEX_MAX_LINES = 20;

  const EXCLUDE_STARTS_WITH = [
      'latex warning: reference', // Reference warning
      'latex warning: there were undefined references', // Reference warning
      'latex warning: label(s) may have changed. rerun to get cross-references right', // Reference warning
      '<error-correction level increased from', // qrcode generation
  ];

  const EXCLUDE_OCCURRENCES = [
      'providing info/warning/error messages', // infwarerr package init logging
      '<making error block ', // qrcode generation
      '<interleaving errorblocks of length', // qrcode generation
  ];

  protected bool $errorsResolved = false;

  /** @var ParseError[] */
  protected array $parseErrors = [];

  public function __construct(
      private readonly string $texLocation,
      int $exitCode,
      private ?array $pdfLatexOutput = null,
      ?string $exitCodeText = null
  ) {
    if ($exitCodeText !== null) {
      $exitCodeText = sprintf(' (%s)', $exitCodeText);
    } else {
      $exitCodeText = '';
    }

    $message = 'Something went wrong during the execution of the pdflatex command, as it returned ' . $exitCode . $exitCodeText. '. See the log file (' . explode('.tex', $texLocation)[0] . '.log) for all details.';

    parent::__construct($message, $exitCode);
  }

  /**
   * Return an extended error message together with the extracted errors
   *
   * @deprecated Use the new object-oriented `getParseErrors` instead
   */
  public function getExtendedMessage(): string
  {
    $this->resolveErrors();

    $message = $this->getMessage();

    if (!empty($this->parseErrors)) {
      $message .= "\n\n\nBelow some more info is tried to extract from the error:\n{$this->combineParseErrorLogSource()}";
    }

    return $message;
  }

  /** @return ParseError[] */
  public function getParseErrors(): array
  {
    $this->resolveErrors();

    return $this->parseErrors;
  }

  /** @deprecated Use the new object-oriented `getParseErrors` instead */
  public function getFilteredTexSource(): string
  {
    $this->resolveErrors();

    $result = ["---"];
    foreach ($this->parseErrors as $parseError) {
      foreach ($parseError->texSnippets as $texSnippet) {
        $result[] = "l.$texSnippet->lineNumber\n" . $texSnippet->getTexSnippet();
      }
    }
    $result[] = "---";

    return implode("\n", $result);
  }

  /** @deprecated Use the new object-oriented `getParseErrors` instead */
  public function getFilteredLogSource(): string
  {
    $this->resolveErrors();

    return $this->combineParseErrorLogSource();
  }

  /**
   * Try to find useful information on the error that has occurred
   * This is stored in the object properties $filteredLogSource and $filteredTexSource
   */
  protected function resolveErrors(): void
  {
    // Check whether already resolved
    if ($this->errorsResolved) {
      return;
    }

    // Otherwise mark as resolved and start process.
    $this->errorsResolved = true;

    array_walk($this->pdfLatexOutput, function ($value, $key) {
      // Find lines with an error
      if (preg_match_all('/error|missing|not found|undefined|too many|runaway|\$|you can\'t use|invalid|^! /ui', $value) <= 0) {
        return;
      }

      // Test matches for exclusions
      $lowerCaseLogLine = strtolower($this->pdfLatexOutput[$key]);
      foreach (self::EXCLUDE_STARTS_WITH as $excludeLine) {
        if (str_starts_with($lowerCaseLogLine, $excludeLine)) {
          return;
        }
      }
      foreach (self::EXCLUDE_OCCURRENCES as $excludeLine) {
        if (str_contains($lowerCaseLogLine, $excludeLine)) {
          return;
        }
      }

      // Get the lines surrounding the error, but do not include empty lines
      // Get lines before the error
      $logLines = [];
      for ($count = 0, $i = 0; $count < self::LOG_GET_LINES && $i < self::LOG_MAX_LINES; $i++) {
        if (!isset($this->pdfLatexOutput[$key - $i])) {
          break;
        }

        if ($value = trim(preg_replace('/\s+/', ' ', $this->pdfLatexOutput[$key - $i]))) {
          array_unshift($logLines, $value);
          $count++;
        }
      }

      // Get lines after the error
      for ($count = 0, $i = 1; $count < self::LOG_GET_LINES && $i < self::LOG_MAX_LINES; $i++) {
        if (!isset($this->pdfLatexOutput[$key + $i])) {
          break;
        }

        if ($value = trim(preg_replace('/\s+/', ' ', $this->pdfLatexOutput[$key + $i]))) {
          $logLines[] = $value;
          $count++;
        }
      }

      $this->parseErrors[] = new ParseError($logLines);
    });

    if (!$this->texLocation) {
      // No tex file to search for matching sources
      return;
    }

    $texFile = new SplFileObject($this->texLocation);
    foreach ($this->parseErrors as $idx => $parseError) {
      /** @var TexSnippet[] $texSnippets */
      $texSnippets = [];

      // Find line numbers in the parsed error
      foreach ($parseError->logLines as $logLine) {
        preg_match('/l\.(\d+)/ui', $logLine, $lineNumberMatch);
        if (count($lineNumberMatch) != 2) {
          continue;
        }

        $lineNumber = (int)$lineNumberMatch[1];

        /** @var TexLine[] $texLines */
        $texLines = [];

        // Get lines before the line number
        for ($count = 0, $i = 0; $count < self::TEX_GET_LINES && $i < self::TEX_MAX_LINES; $i++) {
          $currentLineNumber = $lineNumber - $i;
          $texFile->seek($currentLineNumber);
          if (!$texFile->valid()) {
            break;
          }

          if ($value = trim(preg_replace('/\s+/', ' ', $texFile->current()))) {
            array_unshift($texLines, new TexLine($currentLineNumber, $value));
            $count++;
          }
        }

        // Get lines after the line number
        for ($count = 0, $i = 1; $count < self::TEX_GET_LINES && $i < self::TEX_MAX_LINES; $i++) {
          $currentLineNumber = $lineNumber + $i;
          $texFile->seek($currentLineNumber);
          if (!$texFile->valid()) {
            break;
          }

          if ($value = trim(preg_replace('/\s+/', ' ', $texFile->current()))) {
            $texLines[] = new TexLine($currentLineNumber, $value);
            $count++;
          }
        }

        $texSnippets[] = new TexSnippet($lineNumber, $texLines);
      }

      $this->parseErrors[$idx] = new ParseError($parseError->logLines, $texSnippets);
    }
  }

  private function combineParseErrorLogSource(): string
  {
    $logSource = implode(
        "\n---\n",
        array_map(static fn(ParseError $pe): string => $pe->getLogSource(), $this->parseErrors)
    );

    return implode("\n", ['---', $logSource, '---']);
  }
} 
