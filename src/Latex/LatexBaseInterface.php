<?php

namespace Bobv\LatexBundle\Latex;

interface LatexBaseInterface extends LatexInterface
{
  /** Should return the filename for the pdf file.
   *
   * @return non-empty-string
   */
  public function getFileName(): string;

  /**
   * In case you want to change the filename, use this method.
   *
   * @param non-empty-string $fileName
   */
  public function setFileName(string $fileName): self;

  /**
   * Should return an array with dependency locations.
   *
   * @return array<non-empty-string>
   */
  public function getDependencies(): array;

  /**
   * To add a dependency location.
   *
   * @param non-empty-string $dependency
   */
  public function addDependency(string $dependency): self;

  /**
   * To add multiple dependencies locations.
   *
   * @param iterable<non-empty-string> $dependencies
   */
  public function addDependencies(iterable $dependencies): self;

  /**
   * Should return all registered linked dependencies.
   *
   * @return array<non-empty-string, non-empty-string>
   */
  public function getLinkedDependencies(): array;

  /**
   * @param non-empty-string $dependency
   *
   * @return non-empty-string
   */
  public function addLinkedDependency(string $dependency): string;

  /** Add a package to include */
  public function addPackage(string $package, string $options = ''): self;

  /**
   * Add multiple packages to include (without options).
   *
   * @param iterable<non-empty-string> $packages
   */
  public function addPackages(iterable $packages): self;
}
