<?php
namespace Bobv\LatexBundle\Latex\Section;

use Bobv\LatexBundle\Latex\LatexSection;

class SubSubSection extends LatexSection {

  /**
   * Constructor of the subsubsection section, also defines the default
   *
   * @param string $subsubsectionTitle
   */
  public function __construct($subsubsectionTitle = ''){
    $this->template = '@BobvLatex/Section/sub_sub_section.tex.twig';
    $this->params = array(
      'subsubsectionTitle' => $subsubsectionTitle,
      'includeTOC' => true,

      'newpage' => false, // Standard a subsubsection does not start on a new page

      'extra_commands' => array(), // Define extra commands at the begin of the section
    );
  }
}
