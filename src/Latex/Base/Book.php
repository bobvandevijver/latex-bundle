<?php

namespace Bobv\LatexBundle\Latex\Base;

/**
 * Class LatexBook
 * Base Book
 *
 * @author BobV
 */
class Book extends Base
{

  /**
   * Book constructor, sets defaults
   *
   * @param string $filename
   */
  public function __construct($filename)
  {
    // Define standard values
    $this->template = '@BobvLatex/Base/book.tex.twig';

    // Call parent constructor
    parent::__construct($filename);
  }

}
