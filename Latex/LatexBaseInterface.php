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
   * @return LatexBaseInterface
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
   * @param $dependency
   *
   * @return LatexBaseInterface
   */
  public function addDependency($dependency);

  /**
   * To add multiple dependencies locations
   *
   * @param $dependencies
   *
   * @return LatexBaseInterface
   */
  public function addDependencies($dependencies);

  /**
   * Add an package to include
   *
   * @param $package
   * @param $options
   *
   * @return LatexBaseInterface $this
   */
  public function addPackage($package, $options = '');

  /**
   * Add multiple packages to include (without options)
   *
   * @param $packages
   *
   * @return mixed
   */
  public function addPackages($packages);

}
