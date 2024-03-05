<?php

namespace Bobv\LatexBundle\Latex\Base;

use Bobv\LatexBundle\Latex\LatexBase;

/**
 * @internal Note that this class does not define a template!
 *           It currently is only used by Article and Book as base to avoid code duplication
 *
 * @author BobV
 */
abstract class Base extends LatexBase
{
  public function __construct(string $filename)
  {
    // Define standard values
    $dateTime       = new \DateTime();
    $this->params   = [
        'options'        => null,

        'lhead'          => '', // Top left header
        'chead'          => '', // Top center header
        'rhead'          => '', // Top right header
        'headheight'     => '12pt',

        'lfoot'          => $dateTime->format('d-m-Y G:i'), // Bottom left footer
        'cfoot'          => '', // Bottom center footer
        'rfoot'          => 'Page\ \thepage\ of\ \pageref{LastPage}', // Bottom right footer
        'footskip'       => '20pt',

        'topmargin'      => '-0.45in', // Some document margins
        'evensidemargin' => '0in',
        'oddsidemargin'  => '0in',
        'textwidth'      => '6.5in',
        'textheight'     => '9.0in',
        'headsep'        => '0.25in',

        'linespread'     => '1.1', // Line spacing

        'headrulewidth'  => '0.4pt', // Header size
        'footrulewidth'  => '0.4pt', // Footer size

        'parindent'      => '0pt', // Remove parindentation

        'secnumdepth'    => '0', // Remove section numbers

        'tocdepth'       => '2', // TOC depth

        'extra_commands' => [], // Define extra commands if needed
        'packages'       => [], // Define extra packages to use
    ];

    // Use the ulem package
    $this->addPackage('ulem');

    // Call parent constructor
    parent::__construct($filename);
  }
}
