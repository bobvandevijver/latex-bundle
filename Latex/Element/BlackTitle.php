<?php
namespace BobV\LatexBundle\Latex\Element;

use BobV\LatexBundle\Latex\LatexElement;

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
    $this->template = 'BobVLatexBundle:Element:blacktitle.tex.twig';
    $this->params   = array(
        'title'          => $title,
        'extra_commands' => array(),
    );
  }

} 