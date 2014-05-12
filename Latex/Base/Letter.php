<?php

namespace BobV\LatexBundle\Latex\Base;

use BobV\LatexBundle\Latex\LatexBase;

/**
 * Class LatexLetter
 * Base letter
 *
 * @author BobV
 */
class Letter extends LatexBase
{

  /**
   * Letter constructor, sets defaults
   *
   * @param string $filename
   */
  public function __construct($filename)
  {
    // Define standard values
    $this->template = 'BobVLatexBundle:Base:letter.tex.twig';
    $datetime = new \DateTime();
    $this->params   = array(
        'pagenumber'     => 'false',
        'parskip'        => 'full',
        'fromalign'      => 'right',
        'foldmarks'      => 'false',
        'addrfield'      => 'true',
        'paper'          => 'a4',

        'left'           => '2cm',
        'right'          => '2cm',
        'top'            => '1cm',
        'bottom'         => '2cm',

        'toaddrvpos'     => '3cm',
        'toaddrhpos'     => '2.5cm',
        'refvpos'        => '7.5cm',

        'date'           => $datetime->format('d-m-Y'),

        'extra_commands' => array(), // Define extra commands if needed
        'packages'       => array(), // Define extra packages to use
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
    if (count($matches[1]) > 0) {
      $package = $matches[1][0];
    }

    $this->setParam('packages', $package);

    return $this;
  }

}