<?php

namespace Bobv\LatexBundle\Latex;

use Bobv\LatexBundle\Exception\LatexException;
use Bobv\LatexBundle\Exception\TemplateNotSetException;
use InvalidArgumentException;
use Symfony\Component\Uid\UuidV4;

class LatexBase extends LatexParams implements LatexBaseInterface
{
  /** @var array<covariant LatexInterface> */
  protected array $elements = [];
  /** @var non-empty-string */
  protected string $fileName; // Set in constructor
  protected ?string $template = null;
  /** @var array<non-empty-string> */
  protected array $dependencies = [];
  /** @var array<non-empty-string, non-empty-string> */
  protected array $linkedDependencies = [];

  /** @param non-empty-string $filename */
  public function __construct(string $filename)
  {
    $this->setFileName($filename);
  }

  /** @throws LatexException */
  public function addElement(LatexInterface $latexInterface): self
  {
    if ($latexInterface instanceof LatexBaseInterface) {
      throw new LatexException('A base LaTeX object can not have another base LaTeX object as element!');
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
      ],
    );
  }

  /** @return array<covariant LatexInterface> */
  public function getElements(): array
  {
    return $this->elements;
  }

  /** @param array<covariant LatexInterface> $elements */
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
    return $this->template ?? throw new TemplateNotSetException();
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

  public function addDependency(string $dependency): self
  {
    $this->dependencies[] = $dependency;

    return $this;
  }

  public function addDependencies(iterable $dependencies): self
  {
    foreach ($dependencies as $dependency) {
      $this->addDependency($dependency);
    }

    return $this;
  }

  public function getLinkedDependencies(): array
  {
    return $this->linkedDependencies;
  }

  public function addLinkedDependency(string $dependency): string
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

  public function addPackage(string $package, string $options = ''): self
  {
    $matches = [];
    preg_match_all('/\\\usepackage\{([^}]+)}/u', $package, $matches);
    if (count($matches[1]) > 0) {
      $package = $matches[1][0];
    }

    $this->params['packages'][] = [
      'p' => $package,
      'o' => $options,
    ];

    return $this;
  }

  public function addPackages(iterable $packages): self
  {
    foreach ($packages as $package) {
      $this->addPackage($package);
    }

    return $this;
  }
}
