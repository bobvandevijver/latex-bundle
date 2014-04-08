<?php

namespace BobV\LatexBundle\Latex;

/**
 * Class LatexContext
 * Use this to predefine the context methods and vars
 *
 * @author BobV
 */
class LatexParams {

  /** @var array */
  protected $params = array();

  /**
   * Return the params
   * @return array
   */
  public function getParams(){
    return $this->params;
  }

  /**
   * @param string $param
   * @param string $value
   *
   * @return LatexInterface $this
   * @throws \BobV\LatexBundle\Exception\LatexNotImplementedException
   */
  public function setParam($param, $value){
    if(array_key_exists($this->params, $param)){
      $this->params[$param] = $value;
    }
    return $this;
  }
} 