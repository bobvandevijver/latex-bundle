<?php

namespace Bobv\LatexBundle\Latex\Element;

use Bobv\LatexBundle\Latex\LatexElement;

/**
 * Custom element
 *
 * @author BobV
 */
class CustomElement extends LatexElement
{

  /**
   * Constructor of the custom element, also defines the defaults
   */
  public function __construct(string $custom)
  {
    // Define defaults
    $this->template = '@BobvLatex/Element/custom_element.tex.twig';
    $this->params   = [
        'custom' => $custom,
    ];
  }

}
