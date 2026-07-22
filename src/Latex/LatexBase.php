<?php

namespace Bobv\LatexBundle\Latex;

use Bobv\LatexBundle\Exception\LatexException;
use InvalidArgumentException;
use Symfony\Component\Uid\UuidV4;

class LatexBase extends LatexParams implements LatexBaseInterface
{
  protected array $elements = [];
  protected string $fileName; // Set in constructor
  protected ?string $template = null;
  protected array $dependencies = [];
  /** @var array<non-empty-string, non-empty-string> */
  protected array $linkedDependencies = [];

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

  /** @return array<non-empty-string, non-empty-string> */
  public function getLinkedDependencies(): array
  {
    return $this->linkedDependencies;
  }

  /** @return non-empty-string */
  public function addLinkedDependency(mixed $dependency): string
  {
    if (!is_readable($dependency)) {
      throw new InvalidArgumentException('The dependency must be a readable file.');
    }

    $key = (new UuidV4())->toString();
    if ($ext = pathinfo($dependency, PATHINFO_EXTENSION)) {
      $key .= '.' . $ext;
    }

    $this->linkedDependencies[$key] = $dependency;

    return $key;
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
