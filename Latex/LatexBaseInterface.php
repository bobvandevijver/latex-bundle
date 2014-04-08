<?php
namespace BobV\LatexBundle\Latex;

interface LatexBaseInterface extends LatexInterface
{
  /**
   * Constructur
   *
   * @param string $fileName
   */
  public function __construct($fileName);

  /**
   * Should return the filename for the pdf file
   *
   * @return string
   */
  public function getFileName();

  /**
   * In case you want to change the filename, use this method
   *
   * @param string $fileName
   *
   * @return LatexInterface
   */
  public function setFileName($fileName);
} 