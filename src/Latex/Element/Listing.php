<?php

namespace Bobv\LatexBundle\Latex\Element;

use Bobv\LatexBundle\Latex\LatexElement;

/** Base list element. */
class Listing extends LatexElement
{
  /**
   * Constructor of the listing element. Also defines the defaults.
   *
   * @param string[] $list
   */
  public function __construct(array $list, bool $enumerate = false)
  {
    // Define defaults
    $this->template = '@BobvLatex/Element/listing.tex.twig';
    $this->params   = [
      'list'           => $list,
      'extra_commands' => [],
      'enumerate'      => $enumerate,
    ];
  }
}
