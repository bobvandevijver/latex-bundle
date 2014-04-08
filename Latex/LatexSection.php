<?php
namespace BobV\LatexBundle\Latex;

use BobV\LatexBundle\Exception\LatexException;

class LatexSection extends LatexParams implements LatexInterface
{
  /** @var array */
  protected $elements = array();
  /** @var string */
  protected $template;

  /**
   * @param LatexInterface $latexInterface
   *
   * @return $this
   * @throws \BobV\LatexBundle\Exception\LatexException
   */
  public function addElement(LatexInterface $latexInterface)
  {
    if ($latexInterface instanceof LatexBaseInterface) {
      throw new LatexException("A base LaTeX object can not have another base LaTeX object as element!");
    }

    $this->elements[] = $latexInterface;

    return $this;
  }

  /**
   * @return array
   */
  public function getContext()
  {
    return array_merge(
        $this->getParams(),
        array(
            'elements' => $this->getElements(),
        )
    );
  }

  /**
   * @return array
   */
  public function getElements()
  {
    return $this->elements;
  }

  /**
   * @param array $elements
   */
  public function setElements($elements)
  {
    $this->elements = $elements;
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