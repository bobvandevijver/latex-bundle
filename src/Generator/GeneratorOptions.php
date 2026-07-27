<?php

namespace Bobv\LatexBundle\Generator;

use DateTimeImmutable;

class GeneratorOptions
{
  public const DEFAULT_TIMEOUT = 60;

  /**
   * @param bool                   $forceRegenerate whether to force regeneration of the TeX/PDF file(s), even if it already exists
   * @param array<string, mixed>   $compileOptions  Optional compile options for pdflatex
   * @param bool                   $shellEscape     whether to allow arbitrary program execution outside of the LaTeX file
   * @param string|null            $openInAny       value for the openin_any option, or null to disable the option
   * @param string|null            $openOutAny      value for the openout_any option, or null to disable the option
   * @param string|null            $cacheDir        value for the cache directory, or null to use the default
   * @param DateTimeImmutable|null $maxAge          maximum age of the cache file, or null to use the default
   * @param float|int|null         $timeout         maximum time in seconds to wait for the process to complete, or null to disable. Default is 60 seconds.
   */
  public function __construct(
    public bool $forceRegenerate = false,
    public array $compileOptions = [],
    public bool $shellEscape = false,
    public ?string $openInAny = 'p',
    public ?string $openOutAny = 'p',
    public ?string $cacheDir = null,
    public ?DateTimeImmutable $maxAge = null,
    public float|int|null $timeout = self::DEFAULT_TIMEOUT,
  ) {
  }
}
