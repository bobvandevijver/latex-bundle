<?php

namespace BobV\LatexBundle\Latex\Element;

use BobV\LatexBundle\Latex\LatexElement;

/**
 * Class Graphic
 * Base graphic element
 *
 * @author BobV
 */
class Graphic extends LatexElement
{

  /**
   * Constructor of the graphic element, also defines the defaults
   *
   * @param string $graphic_location
   * @param string $caption
   */
  public function __construct($graphic_location, $caption = false)
  {
    // Define defaults
    $this->template = 'BobVLatexBundle:Element:graphic.tex.twig';
    $this->params   = array(
        'placement'      => 'ht!',
        'centering'      => true,
        'location'       => $graphic_location,
        'width'          => '\textwidth',
        'options'        => '',
        'caption'        => $caption,
        'label'          => 'fig:' . basename($graphic_location),
        'extra_commands' => array(),
    );
  }

}
