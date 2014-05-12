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

  /**
   * Should return an array with dependency locations
   *
   * @return array
   */
  public function getDependencies();

  /**
   * To add an dependency location
   *
   * @param $depedency
   *
   * @return LatexInterface
   */
  public function addDependency($depedency);
} 