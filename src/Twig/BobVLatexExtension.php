<?php

namespace BobV\LatexBundle\Twig;

use BobV\LatexBundle\Helper\Parser;
use function Symfony\Component\String\u;

/**
 * Class BobVLatexExtension
 *
 * @author BobV
 */
class BobVLatexExtension extends AbstractBobVLatexExtension
{

  /** @var Parser */
  private $parser;
  /** @var bool */
  private $useSymfonyString;

  public function __construct(bool $useSymfonyString)
  {
    $this->parser           = new Parser();
    $this->useSymfonyString = $useSymfonyString;
  }

  /**
   * @return array
   */
  public function getFilters()
  {
    $filterClass = class_exists('\\Twig\\TwigFilter') ? '\\Twig\\TwigFilter' : '\\Twig_SimpleFilter';

    return [
        new $filterClass('latex_escape', [$this, 'latexEscape'], ['is_safe' => ['all']]),
        new $filterClass('latex_escape_all', [$this, 'latexEscapeAll'], ['is_safe' => ['all']]),
        new $filterClass('latex_parse_html', [$this->parser, 'latexParseHtml'], ['is_safe' => ['all']]),
    ];
  }

  /**
   * @return string
   */
  public function getName()
  {
    return 'bobv_latex_twig_extension';
  }

  private function passThroughSfString($text)
  {
    if (!$this->useSymfonyString) {
      return $text;
    }

    return u($text)->ascii()->toString();
  }

  /**
   * @param string $text
   *
   * @return string
   */
  public function latexEscape($text, $checkTable = true, $removeLatex = false, $parseNewLines = false, $removeGreek = false)
  {
    return $this->passThroughSfString(
        $this->parser->parseText($text, $checkTable, $removeLatex, $parseNewLines, $removeGreek)
    );
  }

  /**
   * Proxy method to set some params for the parseText call
   *
   * @param string $text
   *
   * @return string
   */
  public function latexEscapeAll($text)
  {
    return $this->passThroughSfString(
        $this->parser->parseText($text, false, true, true, true)
    );
  }

  /**
   * @param string $text
   *
   * @return string
   */
  public function latexParseHtml($text)
  {
    return $this->passThroughSfString(
        $this->parser::parseHtml($text)
    );
  }
}
