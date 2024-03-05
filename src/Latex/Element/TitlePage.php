<?php

namespace Bobv\LatexBundle\Latex\Element;

use Bobv\LatexBundle\Latex\LatexElement;

/**
 * Base title page element
 *
 * @author BobV
 */
class TitlePage extends LatexElement
{
  /**
   * Constructor of the title page element, also defines the defaults
   */
  public function __construct(string $title, string $subtitle = '', string $author = '', string $date = '')
  {
    // Define defaults
    $this->template = '@BobvLatex/Element/titlepage.tex.twig';
    $this->params   = [
        'title'           => $title,
        'subtitle'        => $subtitle,
        'author'          => $author,
        'date'            => $date,

        'vspace'          => '2in',
        'vspace_subtitle' => '0.1in',

        'extra_commands'  => [],
    ];
  }
}
