<?php

namespace BobV\LatexBundle\Latex\Element;

use BobV\LatexBundle\Latex\LatexElement;

/**
 * Class TitlePage
 * Base title page element
 *
 * @author BobV
 */
class TitlePage extends LatexElement
{

  /**
   * Constructor of the title page element, also defines the defaults
   */
  public function __construct($title)
  {
    // Define defaults
    $this->template = 'BobVLatexBundle:Element:titlepage.tex.twig';
    $this->params   = array(
        'title'           => $title,
        'subtitle'        => '',
        'author'          => '',
        'date'            => '',

        'vspace'          => '2in',
        'vspace_subtitle' => '0.1in',


        'extra_commands'  => array(),
    );
  }

} 