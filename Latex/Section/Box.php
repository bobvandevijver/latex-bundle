<?php
namespace BobV\LatexBundle\Latex\Section;

use BobV\LatexBundle\Latex\LatexSection;

class Box extends LatexSection {

  /**
   * Constructor of the section section, also defines the default
   */
  public function __construct(){
    $this->template = 'BobVLatexBundle:Section:box.tex.twig';
    $this->params = array(

      'newpage' => false, // Standard a section starts on a new page

      'extra_commands' => array(), // Define extra commands at the begin of the section
    );
  }
}
