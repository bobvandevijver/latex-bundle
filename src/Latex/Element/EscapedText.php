<?php

namespace BobV\LatexBundle\Latex\Element;

use BobV\LatexBundle\Latex\LatexElement;

/**
 * Class EscapedText
 * Base text element (fully escaped)
 *
 * @author BobV
 */
class EscapedText extends LatexElement
{

  /**
   * Constructor of the title element, also defines the defaults
   *
   * @param string $text
   */
  public function __construct($text)
  {
    // Define defaults
    $this->template = '@BobVLatex/Element/escaped_text.tex.twig';
    $this->params   = array(
        'text'           => $text,
        'extra_commands' => array(),
    );
  }

}
