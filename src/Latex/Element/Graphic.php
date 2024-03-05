<?php

namespace Bobv\LatexBundle\Latex\Element;

use Bobv\LatexBundle\Latex\LatexElement;

/**
 * Base graphic element
 *
 * @author BobV
 */
class Graphic extends LatexElement
{

  /**
   * Constructor of the graphic element, also defines the defaults
   */
  public function __construct(string $graphic_location, string|bool $caption = false)
  {
    // Define defaults
    $this->template = '@BobvLatex/Element/graphic.tex.twig';
    $this->params   = [
        'placement'      => 'ht!',
        'centering'      => true,
        'location'       => $graphic_location,
        'width'          => '\textwidth',
        'options'        => '',
        'caption'        => $caption,
        'label'          => 'fig:' . basename($graphic_location),
        'extra_commands' => [],
    ];
  }

}
