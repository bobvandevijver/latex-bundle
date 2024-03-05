<?php
namespace Bobv\LatexBundle\Latex\Section;

use Bobv\LatexBundle\Latex\LatexSection;

class Box extends LatexSection
{
  /**
   * Constructor of the box section, also defines the default
   */
  public function __construct() {
    $this->template = '@BobvLatex/Section/box.tex.twig';
    $this->params = [

      'newpage' => false, // Standard a section starts on a new page

      'extra_commands' => [], // Define extra commands at the beginning of the section
    ];
  }
}
