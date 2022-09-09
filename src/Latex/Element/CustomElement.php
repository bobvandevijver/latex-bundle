<?php

namespace Bobv\LatexBundle\Latex\Element;

use Bobv\LatexBundle\Latex\LatexElement;

/**
 * Class CustomElement
 * Custom element
 *
 * @author BobV
 */
class CustomElement extends LatexElement
{

  /**
   * Constructor of the custom element, also defines the defaults
   *
   * @param string $custom
   */
  public function __construct($custom)
  {
    // Define defaults
    $this->template = '@BobvLatex/Element/custom_element.tex.twig';
    $this->params   = array(
        'custom'           => $custom,
    );
  }

}
