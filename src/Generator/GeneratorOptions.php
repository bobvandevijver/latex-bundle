<?php

namespace Bobv\LatexBundle\Generator;

class GeneratorOptions
{
  /**
   * @param bool                 $forceRegenerate whether to force regeneration of the TeX/PDF file(s), even if it already exists
   * @param array<string, mixed> $compileOptions  Optional compile options for pdflatex
   * @param bool                 $shellEscape     whether to allow arbitrary program execution outside of the LaTeX file
   * @param string|null          $openInAny       value for the openin_any option, or null to disable the option
   * @param string|null          $openOutAny      value for the openout_any option, or null to disable the option
   */
  public function __construct(
    public bool $forceRegenerate = false,
    public array $compileOptions = [],
    public bool $shellEscape = false,
    public ?string $openInAny = 'p',
    public ?string $openOutAny = 'p',
  ) {
  }
}
