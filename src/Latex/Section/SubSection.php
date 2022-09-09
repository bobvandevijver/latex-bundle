<?php
namespace Bobv\LatexBundle\Latex\Section;

use Bobv\LatexBundle\Latex\LatexSection;

class SubSection extends LatexSection {

  /**
   * Constructor of the subsection section, also defines the default
   *
   * @param string $subsectionTitle
   */
  public function __construct($subsectionTitle = ''){
    $this->template = '@BobvLatex/Section/sub_section.tex.twig';
    $this->params = array(
      'subsectionTitle' => $subsectionTitle,
      'includeTOC' => true,

      'newpage' => false, // Standard a subsection does not start on a new page

      'extra_commands' => array(), // Define extra commands at the begin of the section
    );
  }
}
