<?php
namespace Bobv\LatexBundle\Latex\Element;

use Bobv\LatexBundle\Latex\LatexElement;

class Table extends LatexElement
{
  /**
   * @param array $rows Alignment rows, for example array(l,r,c)
   */
  public function __construct(array $rows = [])
  {

    // Define defaults
    $this->template = '@BobvLatex/Element/table.tex.twig';
    $this->params   = [
        'tabularx'       => true,
        'caption'        => null,
        'rows'           => $rows,
        'data'           => [],
        'width'          => '\textwidth',
        'extra_commands' => [],
    ];
  }

  public function addBottomRule(): self
  {
    $this->addRule('\bottomrule');

    return $this;
  }

  public function addMidRule(): self
  {
    $this->addRule('\midrule');

    return $this;
  }

  public function addRow($row): self
  {
    $data = $this->getParams()['data'];
    $data[] = array(
        'newRule' => true,
        'data'    => $row,
    );
    $this->setParam('data', $data);

    return $this;
  }

  public function addTopRule(): self
  {
    $this->addRule('\toprule');

    return $this;
  }

  private function addRule($rule): void
  {
    $data = $this->getParams()['data'];
    $data[] = array(
        'newRule' => false,
        'data'    => $rule,
    );
    $this->setParam('data', $data);
  }
}
