<?php
namespace Bobv\LatexBundle\Latex\Section;

use Bobv\LatexBundle\Latex\LatexSection;

class MiniPage extends LatexSection
{

  /**
   * Constructor of the section minipage, also defines the default
   */
  public function __construct()
  {
    $this->template = '@BobvLatex/Section/minipage.tex.twig';
    $this->params   = array(
        'width'          => '\textwidth', // Width of the minipage

        'newpage'        => false, // Standard a section starts on a new page

        'extra_commands' => array(), // Define extra commands at the begin of the section
    );
  }
}
