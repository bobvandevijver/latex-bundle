<?php
namespace BobV\LatexBundle\Latex\Element;

use BobV\LatexBundle\Exception\DimensionsNotMatchedException;
use BobV\LatexBundle\Latex\LatexElement;
use Doctrine\Common\Util\Debug;

class Table extends LatexElement
{

  /**
   * @param array $rows Alignment rows, for example array(l,r,c)
   * @param array $data Multidimensional array
   */
  public function __construct($rows = array())
  {

    // Define defaults
    $this->template = 'BobVLatexBundle:Element:table.tex.twig';
    $this->params   = array(
        'rows'           => $rows,
        'data'           => array(),
        'width'          => '\textwidth',
        'extra_commands' => array(),
    );
  }

  public function addBottomRule()
  {
    $this->addRule('\bottomrule');

    return $this;
  }

  public function addMidRule()
  {
    $this->addRule('\midrule');

    return $this;
  }

  public function addRow($row)
  {
    $data = $this->getParams()['data'];
    $data[] = array(
        'newRule' => true,
        'data'    => $row,
    );
    $this->setParam('data', $data);

    return $this;
  }

  public function addTopRule()
  {
    $this->addRule('\toprule');

    return $this;
  }

  private function addRule($rule)
  {
    $data = $this->getParams()['data'];
    $data[] = array(
        'newRule' => false,
        'data'    => $rule,
    );
    $this->setParam('data', $data);
  }

} 