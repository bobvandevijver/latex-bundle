<?php
namespace Bobv\LatexBundle\Latex\Element;

use Bobv\LatexBundle\Latex\LatexElement;

class LongTable extends LatexElement
{

  /**
   * @param array $rows Alignment rows, for example array(l,r,c)
   */
  public function __construct($rows = array())
  {

    // Define defaults
    $this->template = '@BobvLatex/Element/longtable.tex.twig';
    $this->params   = array(
        'caption'        => NULL,
        'firsthead'      => NULL,
        'head'           => NULL,
        'foot'           => NULL,
        'lastfoot'       => NULL,
        'rows'           => $rows,
        'data'           => array(),
        'extra_commands' => array(),
    );
  }

  public function addRow($row)
  {
    $data   = $this->getParams()['data'];
    $data[] = array(
        'newRule' => true,
        'data'    => $row,
    );
    $this->setParam('data', $data);

    return $this;
  }

}
