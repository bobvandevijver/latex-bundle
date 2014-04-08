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
    $dateTime = new \DateTime();
    $this->params  = array(
        'lhead' => '', // Top left header
        'chead' => '', // Top center header
        'rhead' => '', // Top right header

        'lfoot' => '', // Bottom left footer
        'cfoot' => $dateTime->format('d-m-Y h:m'), // Bottom center footer
        'rfoot' => 'Page\ \thepage\ of\ \pageref{LastPage}', // Bottom right footer
    );

    // Call parent constructor
    parent::__construct($filename);
  }

}