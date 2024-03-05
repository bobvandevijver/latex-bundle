<?php

namespace Bobv\LatexBundle\Latex\Element;

use Bobv\LatexBundle\Latex\LatexElement;

/**
 * Base list element
 *
 * @author BobV
 */
class Listing extends LatexElement
{

  /**
   * Constructor of the listing element, also defines the defaults
   */
  public function __construct(array $list, bool $enumerate = false) {
    // Define defaults
    $this->template = '@BobvLatex/Element/listing.tex.twig';
    $this->params   = [
        'list'           => $list,
        'extra_commands' => [],
        'enumerate'      => $enumerate,
    ];
  }

}
