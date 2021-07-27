<?php

namespace BobV\LatexBundle\Latex;
use BobV\LatexBundle\Exception\LatexException;

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
   * Set a specific parameter for the class
   *
   * @param string $param
   * @param mixed $value
   *
   * @return LatexInterface $this
   * @throws \BobV\LatexBundle\Exception\LatexException
   */
  public function setParam($param, $value){
    if(array_key_exists($param, $this->params)){
      // Check if the param is defined as array
      if(is_array($this->params[$param])){
        // If the value is an array, replace the complete array, else add a new array element
        if(is_array($value)){
          $this->params[$param] = $value;
        }else{
          $this->params[$param][] = $value;
        }
      }else{
        $this->params[$param] = $value;
      }
    }else{
      throw new LatexException("The param $param is not defined for " . get_class($this));
    }
    return $this;
  }
}
