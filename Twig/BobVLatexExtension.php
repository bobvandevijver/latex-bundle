<?php

namespace BobV\LatexBundle\Twig;

use BobV\LatexBundle\Helper\Parser;

/**
 * Class BobVLatexExtension
 *
 * @author BobV
 */
class BobVLatexExtension extends \Twig_Extension
{

  private $parser;

  public function __construct() {
    $this->parser = new Parser();
  }

  /**
   * @return array
   */
  public function getFilters() {
    return array(
        new \Twig_SimpleFilter('latex_escape', array($this->parser, 'parseText')),
        new \Twig_SimpleFilter('latex_escape_all', array($this, 'latexEscapeAll')),
        new \Twig_SimpleFilter('latex_parse_html', array($this->parser, 'parseHtml')),
    );
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
    return $this->parser->parseText($text, false, true, true);
  }

}
