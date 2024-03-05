<?php

namespace Bobv\LatexBundle\Latex;

use Bobv\LatexBundle\Exception\LatexException;

class LatexBase extends LatexParams implements LatexBaseInterface
{
  protected array $elements = [];
  protected string $fileName; // Set in constructor
  protected ?string $template = null;
  protected array $dependencies = [];

  public function __construct(string $filename)
  {
    $this->setFileName($filename);
  }

  /**
   * @throws LatexException
   */
  public function addElement(LatexInterface $latexInterface): self
  {
    if ($latexInterface instanceof LatexBaseInterface) {
      throw new LatexException("A base LaTeX object can not have another base LaTeX object as element!");
    }

    $this->elements[] = $latexInterface;

    return $this;
  }

  public function getContext(): array
  {
    return array_merge(
        $this->getParams(),
        [
            'elements' => $this->getElements(),
        ]
    );
  }

  public function getElements(): array
  {
    return $this->elements;
  }

  public function setElements(array $elements): void
  {
    $this->elements = $elements;
  }

  public function getFileName(): string
  {
    return $this->fileName;
  }

  public function setFileName(string $fileName): self
  {
    $this->fileName = $fileName;

    return $this;
  }

  public function getTemplate(): string
  {
    return $this->template;
  }

  public function setTemplate(string $template): self
  {
    $this->template = $template;

    return $this;
  }

  public function getDependencies(): array
  {
    return $this->dependencies;
  }

  public function addDependency(mixed $dependency): self
  {
    $this->dependencies[] = $dependency;

    return $this;
  }

  /**
   * To add multiple dependencies locations
   */
  public function addDependencies(iterable $dependencies): self
  {
    foreach ($dependencies as $dependency) {
      $this->addDependency($dependency);
    }

    return $this;
  }

  /**
   * Add a package to include
   */
  public function addPackage(mixed $package, string $options = ''): self
  {
    $matches = array();
    preg_match_all('/\\\usepackage\{([^}]+)}/u', $package, $matches);
    if (count($matches[1]) > 0) {
      $package = $matches[1][0];
    }

    $this->params['packages'][] = array(
        'p' => $package,
        'o' => $options,
    );

    return $this;
  }

  /**
   * Add multiple packages to include (without options)
   */
  public function addPackages(iterable $packages): self
  {
    foreach ($packages as $package) {
      $this->addPackage($package);
    }

    return $this;
  }
}
