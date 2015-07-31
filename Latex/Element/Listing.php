<?php

namespace BobV\LatexBundle\Latex\Element;

use BobV\LatexBundle\Latex\LatexElement;

/**
 * Class Listing
 * Base list element
 *
 * @author BobV
 */
class Listing extends LatexElement
{

  /**
   * Constructor of the listing element, also defines the defaults
   *
   * @param array $list
   */
  public function __construct($list)
  {
    // Define defaults
    $this->template = 'BobVLatexBundle:Element:listing.tex.twig';
    $this->params   = array(
        'list'           => $list,
        'extra_commands' => array(),
    );
  }

}
