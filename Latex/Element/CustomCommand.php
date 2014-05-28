<?php

namespace BobV\LatexBundle\Latex\Element;

use BobV\LatexBundle\Latex\LatexElement;

/**
 * Class CustomCommand
 * Custom command
 *
 * @author BobV
 */
class CustomCommand extends LatexElement
{

  /**
   * Constructor of the custom command, also defines the defaults
   *
   * @param string $text
   */
  public function __construct($custom)
  {
    // Define defaults
    $this->template = 'BobVLatexBundle:Element:custom_command.tex.twig';
    $this->params   = array(
        'custom'           => $custom,
    );
  }

} 