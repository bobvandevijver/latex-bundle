<?php

namespace Bobv\LatexBundle\Latex\Base;

use Bobv\LatexBundle\Latex\LatexBase;

class Standalone extends LatexBase
{
  /**
   * Standalone constructor, sets defaults
   */
  public function __construct(string $filename) {
    // Define standard values
    $this->template = '@BobvLatex/Base/standalone.tex.twig';
    $this->params   = [
        'mode'           => 'crop',  // Define the standalone mode
        'border'         => '1pt',   // Content border
        'varwidth'       => true,    // Variable width mode
        'extra_commands' => [],      // Define extra commands if needed
        'packages'       => [],      // Define extra packages to use
    ];

    // Call parent constructor
    parent::__construct($filename);
  }
}
