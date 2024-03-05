<?php
namespace Bobv\LatexBundle\Latex\Section;

use Bobv\LatexBundle\Latex\LatexSection;

class Section extends LatexSection {

  /**
   * Constructor of the section section, also defines the default
   */
  public function __construct(string $sectionTitle = ''){
    $this->template = '@BobvLatex/Section/section.tex.twig';
    $this->params = [
      'sectionTitle' => $sectionTitle,
      'includeTOC' => true,

      'newpage' => true, // Standard a section starts on a new page

      'extra_commands' => [], // Define extra commands at the beginning of the section
    ];
  }
}
