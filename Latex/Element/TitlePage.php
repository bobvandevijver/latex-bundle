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
   *
   * @param string $title
   * @param string $subtitle
   * @param string $author
   * @param string $date
   */
  public function __construct($title, $subtitle='', $author='', $date = '')
  {
    // Define defaults
    $this->template = '@BobVLatex/Element/titlepage.tex.twig';
    $this->params   = array(
        'title'           => $title,
        'subtitle'        => $subtitle,
        'author'          => $author,
        'date'            => $date,

        'vspace'          => '2in',
        'vspace_subtitle' => '0.1in',


        'extra_commands'  => array(),
    );
  }

}
