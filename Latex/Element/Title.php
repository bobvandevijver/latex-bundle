<?php

namespace BobV\LatexBundle\Latex\Element;

use BobV\LatexBundle\Latex\LatexElement;

/**
 * Class LatexTitle
 * Base title element
 *
 * @author BobV
 */
class Title extends LatexElement
{

  /**
   * Constructor of the title element, also defines the defaults
   */
  public function __construct($title)
  {
    // Define defaults
    $this->template = 'BobVLatexBundle:Element:title.tex.twig';
    $this->params = array(
      'title' => $title,
      'subtitle' => '',
      'author' => '',
      'date' => '',
    );
  }

} 