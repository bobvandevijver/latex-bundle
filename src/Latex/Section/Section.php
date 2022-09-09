<?php
namespace Bobv\LatexBundle\Latex\Section;

use Bobv\LatexBundle\Latex\LatexSection;

class Section extends LatexSection {

  /**
   * Constructor of the section section, also defines the default
   *
   * @param string $sectionTitle
   */
  public function __construct($sectionTitle = ''){
    $this->template = '@BobvLatex/Section/section.tex.twig';
    $this->params = array(
      'sectionTitle' => $sectionTitle,
      'includeTOC' => true,

      'newpage' => true, // Standard a section starts on a new page

      'extra_commands' => array(), // Define extra commands at the begin of the section
    );
  }
}
