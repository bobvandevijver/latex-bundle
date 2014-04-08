<?php
namespace BobV\LatexBundle\Latex\Section;

class Section extends \BobV\LatexBundle\Latex\LatexSection {

  /**
   * Constructor of the section section, also defines the default
   *
   * @param string $sectionTitle
   */
  public function __construct($sectionTitle = ''){
    $this->template = 'BobVLatexBundle:Section:section.tex.twig';
    $this->params = array(
      'sectionTitle' => $sectionTitle,
    );
  }
} 