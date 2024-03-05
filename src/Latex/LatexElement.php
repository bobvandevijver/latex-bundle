<?php
namespace Bobv\LatexBundle\Latex;

class LatexElement extends LatexParams implements LatexInterface
{
  protected string $template;

  public function getContext(): array
  {
    return $this->getParams();
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
