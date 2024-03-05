<?php

namespace Bobv\LatexBundle\Latex\Element;

use Bobv\LatexBundle\Latex\LatexElement;

/**
 * Custom command
 *
 * @author BobV
 */
class CustomCommand extends LatexElement
{

  /**
   * Constructor of the custom command, also defines the defaults
   */
  public function __construct(string $custom)
  {
    // Define defaults
    $this->template = '@BobvLatex/Element/custom_command.tex.twig';
    $this->params   = [
        'custom' => $custom,
    ];
  }

}
