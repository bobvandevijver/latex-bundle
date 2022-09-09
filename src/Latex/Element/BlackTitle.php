<?php
namespace Bobv\LatexBundle\Latex\Element;

use Bobv\LatexBundle\Latex\LatexElement;

/**
 * Class BlackTitle
 *
 * @author BobV
 */
class BlackTitle extends LatexElement
{

  /**
   * @param $title
   */
  public function __construct($title)
  {
    $this->template = '@BobvLatex/Element/blacktitle.tex.twig';
    $this->params   = array(
        'title'          => $title,
        'extra_commands' => array(),
    );
  }

}
