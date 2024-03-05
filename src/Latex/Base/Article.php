<?php

namespace Bobv\LatexBundle\Latex\Base;

/**
 * Base article
 *
 * @author BobV
 */
class Article extends Base
{

  /**
   * Article constructor, sets defaults
   */
  public function __construct(string $filename)
  {
    // Define standard values
    $this->template = '@BobvLatex/Base/article.tex.twig';

    // Call parent constructor
    parent::__construct($filename);
  }

}
