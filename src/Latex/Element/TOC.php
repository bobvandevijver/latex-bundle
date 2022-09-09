<?php
namespace Bobv\LatexBundle\Latex\Element;

use Bobv\LatexBundle\Latex\LatexElement;

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
    $this->template = '@BobvLatex/Element/toc.tex.twig';
  }
}
