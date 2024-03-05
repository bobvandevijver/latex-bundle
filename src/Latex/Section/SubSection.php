<?php
namespace Bobv\LatexBundle\Latex\Section;

use Bobv\LatexBundle\Latex\LatexSection;

class SubSection extends LatexSection
{
  /**
   * Constructor of the subsection section, also defines the default
   */
  public function __construct(string $subsectionTitle = '') {
    $this->template = '@BobvLatex/Section/sub_section.tex.twig';
    $this->params = [
      'subsectionTitle' => $subsectionTitle,
      'includeTOC' => true,

      'newpage' => false, // Standard a subsection does not start on a new page

      'extra_commands' => [], // Define extra commands at the beginning of the section
    ];
  }
}
