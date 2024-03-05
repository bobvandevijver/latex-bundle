<?php
namespace Bobv\LatexBundle\Latex\Section;

use Bobv\LatexBundle\Latex\LatexSection;

class SubSubSection extends LatexSection
{
  /**
   * Constructor of the subsubsection section, also defines the default
   */
  public function __construct(string $subsubsectionTitle = '') {
    $this->template = '@BobvLatex/Section/sub_sub_section.tex.twig';
    $this->params = [
      'subsubsectionTitle' => $subsubsectionTitle,
      'includeTOC' => true,

      'newpage' => false, // Standard a subsubsection does not start on a new page

      'extra_commands' => [], // Define extra commands at the beginning of the section
    ];
  }
}
