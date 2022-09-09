<?php

namespace Bobv\LatexBundle\Latex\Element;

use Bobv\LatexBundle\Latex\LatexElement;

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
    $this->template = '@BobvLatex/Element/custom_command.tex.twig';
    $this->params   = array(
        'custom'           => $custom,
    );
  }

}
