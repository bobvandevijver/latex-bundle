<?php

namespace Bobv\LatexBundle\Latex;

use Bobv\LatexBundle\Exception\LatexException;

class LatexSection extends LatexParams implements LatexInterface
{
  /** @var array<covariant LatexInterface> */
  protected array $elements = [];
  protected string $template;

  /** @throws LatexException */
  public function addElement(LatexInterface $latexInterface): self
  {
    if ($latexInterface instanceof LatexBaseInterface) {
      throw new LatexException('A base LaTeX object can not have another base LaTeX object as element!');
    }

    $this->elements[] = $latexInterface;

    return $this;
  }

  /** @return array<string, mixed> */
  public function getContext(): array
  {
    return array_merge(
      $this->getParams(),
      [
        'elements' => $this->getElements(),
      ]
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

  public function getTemplate(): string
  {
    return $this->template;
  }

  public function setTemplate(string $template): self
  {
    $this->template = $template;

    return $this;
  }
}
