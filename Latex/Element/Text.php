<?php

namespace BobV\LatexBundle\Latex\Element;

use BobV\LatexBundle\Latex\LatexElement;

/**
 * Class LatexTitle
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
    $this->template = 'BobVLatexBundle:Element:text.tex.twig';
    $this->params = array(
      'text' => $text,
    );
  }

} 