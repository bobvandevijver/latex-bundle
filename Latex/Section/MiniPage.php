<?php
namespace BobV\LatexBundle\Latex\Section;

use BobV\LatexBundle\Latex\LatexSection;

class MiniPage extends LatexSection
{

  /**
   * Constructor of the section minipage, also defines the default
   *
   * @param string $sectionTitle
   */
  public function __construct()
  {
    $this->template = 'BobVLatexBundle:Section:minipage.tex.twig';
    $this->params   = array(
        'width'          => '\textwidth', // Width of the minipage

        'newpage'        => false, // Standard a section starts on a new page

        'extra_commands' => array(), // Define extra commands at the begin of the section
    );
  }
} 