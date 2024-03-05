<?php

namespace Bobv\LatexBundle\Twig;

use Bobv\LatexBundle\Helper\Parser;
use function Symfony\Component\String\u;

/**
 * @author BobV
 */
class BobvLatexExtension extends AbstractBobvLatexExtension
{
  private Parser $parser; // Set in constructor

  public function __construct(private readonly bool $useSymfonyString)
  {
    $this->parser = new Parser();
  }

  public function getFilters(): array
  {
    $filterClass = class_exists('\\Twig\\TwigFilter') ? '\\Twig\\TwigFilter' : '\\Twig_SimpleFilter';

    return [
        new $filterClass('latex_escape', [$this, 'latexEscape'], ['is_safe' => ['all']]),
        new $filterClass('latex_escape_all', [$this, 'latexEscapeAll'], ['is_safe' => ['all']]),
        new $filterClass('latex_parse_html', [$this, 'latexParseHtml'], ['is_safe' => ['all']]),
    ];
  }

  public function getName(): string
  {
    return 'bobv_latex_twig_extension';
  }

  private function passThroughSfString(?string $text): ?string
  {
    if (!$this->useSymfonyString) {
      return $text;
    }

    return u($text)->ascii()->toString();
  }

  public function latexEscape(?string $text, bool $checkTable = true, bool $removeLatex = false, bool $parseNewLines = false, bool $removeGreek = false): string
  {
    return $this->passThroughSfString(
        $this->parser->parseText($text, $checkTable, $removeLatex, $parseNewLines, $removeGreek)
    );
  }

  /**
   * Proxy method to set some params for the parseText call
   */
  public function latexEscapeAll(?string $text): ?string
  {
    return $this->passThroughSfString(
        $this->parser->parseText($text, false, true, true, true)
    );
  }

  public function latexParseHtml(?string $text): ?string
  {
    return $this->passThroughSfString(
        $this->parser::parseHtml($text)
    );
  }
}
