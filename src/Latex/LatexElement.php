<?php
namespace Bobv\LatexBundle\Latex;

class LatexElement extends LatexParams implements LatexInterface
{
  /** @var string */
  protected $template;

  public function getContext()
  {
    return $this->getParams();
  }

  /**
   * @return string
   */
  public function getTemplate()
  {
    return $this->template;
  }

  /**
   * @param string $template
   *
   * @return LatexInterface $this
   */
  public function setTemplate($template)
  {
    $this->template = $template;

    return $this;
  }
}
