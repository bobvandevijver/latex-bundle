<?php

namespace Bobv\LatexBundle\Latex\Element;

use Bobv\LatexBundle\Latex\LatexElement;

/**
 * Base text element (fully escaped)
 *
 * @author BobV
 */
class EscapedText extends LatexElement
{

  /**
   * Constructor of the title element, also defines the defaults
   */
  public function __construct(string $text)
  {
    // Define defaults
    $this->template = '@BobvLatex/Element/escaped_text.tex.twig';
    $this->params   = [
        'text'           => $text,
        'extra_commands' => [],
    ];
  }

}
