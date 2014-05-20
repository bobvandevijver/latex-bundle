<?php

namespace BobV\LatexBundle\Latex\Base;

use BobV\LatexBundle\Latex\LatexBase;

/**
 * Class LatexBook
 * Base Book
 *
 * @author BobV
 */
class Book extends LatexBase
{

  /**
   * Book constructor, sets defaults
   *
   * @param string $filename
   */
  public function __construct($filename)
  {
    // Define standard values
    $this->template = 'BobVLatexBundle:Base:book.tex.twig';
    $dateTime       = new \DateTime();
    $this->params   = array(
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

        'extra_commands' => array(), //Define extra commands if needed
        'packages'       => array(), // Define extra packages to use
    );

    // Call parent constructor
    parent::__construct($filename);
  }

}