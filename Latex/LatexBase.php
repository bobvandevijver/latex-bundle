<?php
namespace BobV\LatexBundle\Latex;

use BobV\LatexBundle\Exception\LatexException;

class LatexBase extends LatexParams implements LatexBaseInterface
{
  /** @var array */
  protected $elements = array();
  /** @var string */
  protected $fileName;
  /** @var string */
  protected $template;

  /**
   * @param string $filename
   *
   * @throws \BobV\LatexBundle\Exception\LatexException
   */
  public function __construct($filename)
  {
    $this->setFileName($filename);
  }

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
  public function getFileName()
  {
    return $this->fileName;
  }

  /**
   * @param string $fileName
   *
   * @return LatexInterface $this
   */
  public function setFileName($fileName)
  {
    $this->fileName = $fileName;

    return $this;
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