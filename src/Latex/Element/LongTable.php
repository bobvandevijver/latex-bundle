<?php
namespace Bobv\LatexBundle\Latex\Element;

use Bobv\LatexBundle\Latex\LatexElement;

class LongTable extends LatexElement
{
  /**
   * @param array $rows Alignment rows, for example array(l,r,c)
   */
  public function __construct(array $rows = [])
  {
    // Define defaults
    $this->template = '@BobvLatex/Element/longtable.tex.twig';
    $this->params   = [
        'caption'        => NULL,
        'firsthead'      => NULL,
        'head'           => NULL,
        'foot'           => NULL,
        'lastfoot'       => NULL,
        'rows'           => $rows,
        'data'           => [],
        'extra_commands' => [],
    ];
  }

  public function addRow($row): self
  {
    $data   = $this->getParams()['data'];
    $data[] = [
        'newRule' => true,
        'data'    => $row,
    ];
    $this->setParam('data', $data);

    return $this;
  }

}
