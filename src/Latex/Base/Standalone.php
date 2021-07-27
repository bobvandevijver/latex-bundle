<?php

namespace BobV\LatexBundle\Latex\Base;

use BobV\LatexBundle\Latex\LatexBase;

class Standalone extends LatexBase
{
  /**
   * Standalone constructor, sets defaults
   *
   * @param string $filename
   */
  public function __construct($filename) {
    // Define standard values
    $this->template = '@BobVLatex/Base/standalone.tex.twig';
    $this->params   = array(
        'mode'           => 'crop',  // Define the standalone mode
        'border'         => '1pt',   // Content border
        'varwidth'       => true,    // Variable width mode
        'extra_commands' => array(), // Define extra commands if needed
        'packages'       => array(), // Define extra packages to use
    );

    // Call parent constructor
    parent::__construct($filename);
  }
}
