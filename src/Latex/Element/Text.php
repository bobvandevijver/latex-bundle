<?php

namespace Bobv\LatexBundle\Latex\Element;

use Bobv\LatexBundle\Latex\LatexElement;

/**
 * Base text element
 *
 * @author BobV
 */
class Text extends LatexElement
{
  /**
   * Constructor of the title element, also defines the defaults
   */
  public function __construct(string $text)
  {
    // Define defaults
    $this->template = '@BobvLatex/Element/text.tex.twig';
    $this->params   = [
        'text'           => $text,
        'extra_commands' => [],
    ];
  }
}
