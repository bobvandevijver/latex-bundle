<?php

namespace Bobv\LatexBundle\Latex\Base;

use Bobv\LatexBundle\Latex\LatexBase;

/**
 * Base letter
 *
 * @author BobV
 */
class Letter extends LatexBase
{

  /**
   * Letter constructor, sets defaults
   */
  public function __construct(string $filename)
  {
    // Define standard values
    $this->template = '@BobvLatex/Base/letter.tex.twig';
    $datetime = new \DateTime();
    $this->params   = [
        'pagenumber'     => 'false',     // Whether to print pagenumbers from page 2 and forward
        'parskip'        => 'full',      // Spacing between paragraphs (full, half, ..)
        'fromalign'      => 'right',     // Alignment of the from address
        'foldmarks'      => 'false',     // Whether to print folding marks
        'addrfield'      => 'true',      // Whether to print the address field
        'refline'        => 'dateright', // Position of the date, can be dateleft, dateright, narrow, nodate or wide.
        'paper'          => 'a4',        // Paper size
        'firstfoot'      => 'false',     // First page footer

        'left'           => '2cm',       // Page margins
        'right'          => '2cm',
        'top'            => '1cm',
        'bottom'         => '2cm',

        'toaddrvpos'     => '3cm',       // Positioning
        'toaddrhpos'     => '2.5cm',
        'refvpos'        => '7.5cm',

        'date'           => $datetime->format('d-m-Y'),

        'extra_commands' => [], // Define extra commands if needed
        'packages'       => [], // Define extra packages to use
    ];

    // Use the ulem package
    $this->addPackage('ulem');

    // Call parent constructor
    parent::__construct($filename);
  }

}
