<?php

namespace Bobv\LatexBundle\Latex\Element;

use Bobv\LatexBundle\Latex\LatexElement;

/**
 * Class Text
 * Base text element
 *
 * @author BobV
 */
class Text extends LatexElement
{

  /**
   * Constructor of the title element, also defines the defaults
   *
   * @param string $text
   */
  public function __construct($text)
  {
    // Define defaults
    $this->template = '@BobvLatex/Element/text.tex.twig';
    $this->params   = array(
        'text'           => $text,
        'extra_commands' => array(),
    );
  }

}
