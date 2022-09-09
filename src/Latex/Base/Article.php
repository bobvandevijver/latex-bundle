<?php

namespace Bobv\LatexBundle\Latex\Base;

/**
 * Class LatexArticle
 * Base article
 *
 * @author BobV
 */
class Article extends Base
{

  /**
   * Article constructor, sets defaults
   *
   * @param string $filename
   */
  public function __construct($filename)
  {
    // Define standard values
    $this->template = '@BobvLatex/Base/article.tex.twig';

    // Call parent constructor
    parent::__construct($filename);
  }

}
