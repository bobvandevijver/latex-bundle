<?php
namespace BobV\LatexBundle\Latex\Element;

use BobV\LatexBundle\Latex\LatexElement;

/**
 * Class TOC
 *
 * @author BobV
 */
class TOC extends LatexElement
{

  /**
   * Constructor of the TOC element, also defines the default
   */
  public function __construct()
  {
    // Set defaults
    $this->template = 'BobVLatexBundle:Element:toc.tex.twig';
  }
}
