<?php

namespace Bobv\LatexBundle\Latex\Element;

use Bobv\LatexBundle\Latex\LatexElement;

/**
 * Base letter element
 *
 * @author BobV
 */
class Letter extends LatexElement
{

  /**
   * Constructor of the title element, also defines the defaults
   */
  public function __construct(string $address, string $opening, string $text)
  {
    // Define defaults
    $this->template = '@BobvLatex/Element/letter.tex.twig';
    $this->params   = [
        'address'        => $address, // Address
        'opening'        => $opening, // Opening of the letter
        'text'           => $text,    // Content of the letter

        'extra_commands' => [],  // Define extra commands if needed
    ];
  }

}
