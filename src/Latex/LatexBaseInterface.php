<?php

namespace Bobv\LatexBundle\Latex;

interface LatexBaseInterface extends LatexInterface
{
  public function __construct(string $fileName);

  /**
   * Should return the filename for the pdf file
   */
  public function getFileName(): string;

  /**
   * In case you want to change the filename, use this method
   */
  public function setFileName(string $fileName): self;

  /**
   * Should return an array with dependency locations
   */
  public function getDependencies(): array;

  /**
   * To add a dependency location
   */
  public function addDependency(mixed $dependency): self;

  /**
   * To add multiple dependencies locations
   */
  public function addDependencies(iterable $dependencies): self;

  /**
   * Add a package to include
   */
  public function addPackage(mixed $package, string $options = ''): self;

  /**
   * Add multiple packages to include (without options)
   */
  public function addPackages(iterable $packages): self;

}
