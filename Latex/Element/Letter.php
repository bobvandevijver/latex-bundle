<?php

namespace BobV\LatexBundle\Latex\Element;

use BobV\LatexBundle\Latex\LatexElement;

/**
 * Class Letter
 * Base letter element
 *
 * @author BobV
 */
class Letter extends LatexElement
{

  /**
   * Constructor of the title element, also defines the defaults
   *
   * @param string $address
   * @param string $opening
   * @param string $text
   */
  public function __construct($address, $opening, $text)
  {
    // Define defaults
    $this->template = 'BobVLatexBundle:Element:letter.tex.twig';
    $this->params   = array(
        'address'        => $address, // Address
        'opening'        => $opening, // Opening of the letter
        'text'           => $text,    // Content of the letter

        'extra_commands' => array(),  // Define extra commands if needed
    );
  }

}
