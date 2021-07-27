<?php

namespace BobV\LatexBundle\Twig;

use BobV\LatexBundle\Helper\Parser;

/**
 * Class BobVLatexExtension
 *
 * @author BobV
 */
class BobVLatexExtension extends AbstractBobVLatexExtension
{

  private $parser;

  public function __construct() {
    $this->parser = new Parser();
  }

  /**
   * @return array
   */
  public function getFilters() {
    $filterClass = class_exists('\\Twig\\TwigFilter') ? '\\Twig\\TwigFilter' : '\\Twig_SimpleFilter';
    return [
        new $filterClass('latex_escape', [$this->parser, 'parseText'], ['is_safe' => ['all']]),
        new $filterClass('latex_escape_all', [$this, 'latexEscapeAll'], ['is_safe' => ['all']]),
        new $filterClass('latex_parse_html', [$this->parser, 'parseHtml'], ['is_safe' => ['all']]),
    ];
  }

  /**
   * @return string
   */
  public function getName() {
    return 'bobv_latex_twig_extension';
  }

  /**
   * Proxy method to set some params for the parseText call
   *
   * @param $text
   *
   * @return mixed
   */
  public function latexEscapeAll($text) {
    return $this->parser->parseText($text, false, true, true, true);
  }

}
