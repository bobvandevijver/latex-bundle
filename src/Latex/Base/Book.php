<?php

namespace Bobv\LatexBundle\Latex\Base;

/**
 * Base Book
 *
 * @author BobV
 */
class Book extends Base
{

  /**
   * Book constructor, sets defaults
   */
  public function __construct(string $filename)
  {
    // Define standard values
    $this->template = '@BobvLatex/Base/book.tex.twig';

    // Call parent constructor
    parent::__construct($filename);
  }

}
