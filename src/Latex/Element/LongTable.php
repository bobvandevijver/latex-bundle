<?php

namespace Bobv\LatexBundle\Latex\Element;

use Bobv\LatexBundle\Latex\LatexElement;

class LongTable extends LatexElement
{
  /** @param string[] $rows Alignment rows, for example array(l,r,c) */
  public function __construct(array $rows = [])
  {
    // Define defaults
    $this->template = '@BobvLatex/Element/longtable.tex.twig';
    $this->params   = [
      'caption'        => null,
      'firsthead'      => null,
      'head'           => null,
      'foot'           => null,
      'lastfoot'       => null,
      'rows'           => $rows,
      'data'           => [],
      'extra_commands' => [],
    ];
  }

  public function addRow(mixed $row): self
  {
    /** @var array<int, mixed> $data */
    $data   = $this->getParams()['data'] ?? [];
    $data[] = [
      'newRule' => true,
      'data'    => $row,
    ];
    $this->setParam('data', $data);

    return $this;
  }
}
