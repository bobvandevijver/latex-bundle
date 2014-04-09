<?php

namespace BobV\LatexBundle\Latex\Base;

use BobV\LatexBundle\Latex\LatexBase;

/**
 * Class LatexArticle
 * Base article
 *
 * @author BobV
 */
class Article extends LatexBase
{

  /**
   * Article constructor, sets defaults
   *
   * @param string $filename
   */
  public function __construct($filename)
  {
    // Define standard values
    $this->template = 'BobVLatexBundle:Base:article.tex.twig';
    $dateTime       = new \DateTime();
    $this->params   = array(
        'lhead'          => '', // Top left header
        'chead'          => '', // Top center header
        'rhead'          => '', // Top right header
        'headheight'     => '12pt',

        'lfoot'          => $dateTime->format('d-m-Y h:m'), // Bottom left footer
        'cfoot'          => '', // Bottom center footer
        'rfoot'          => 'Page\ \thepage\ of\ \pageref{LastPage}', // Bottom right footer
        'footskip'       => '20pt',

        'packages'       => array(), // Define extra packages to use

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
    );

    // Call parent constructor
    parent::__construct($filename);
  }

  /**
   * Add an package to include
   *
   * @param $package
   *
   * @return $this
   */
  public function addPackage($package)
  {
    $matches = array();
    preg_match_all('/\\\usepackage\{([^}]+)\}/u', $package, $matches);
    if(count($matches[1]) > 0){
      $package = $matches[1][0];
    }

    $this->setParam('packages', $package);

    return $this;
  }

}