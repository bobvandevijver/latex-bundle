<?php

namespace Bobv\LatexBundle\Latex;
use Bobv\LatexBundle\Exception\LatexException;

/**
 * Use this to predefine the context methods and vars
 *
 * @author BobV
 */
class LatexParams
{
  protected array $params = [];

  public function getParams(): array {
    return $this->params;
  }

  /**
   * Set a specific parameter for the class
   *
   * @throws \Bobv\LatexBundle\Exception\LatexException
   */
  public function setParam(string $param, mixed $value): LatexInterface {
    if (array_key_exists($param, $this->params)) {
      // Check if the param is defined as array
      if (is_array($this->params[$param])) {
        // If the value is an array, replace the complete array, else add a new array element
        if (is_array($value)) {
          $this->params[$param] = $value;
        } else {
          $this->params[$param][] = $value;
        }
      } else {
        $this->params[$param] = $value;
      }
    } else {
      throw new LatexException("The param $param is not defined for " . get_class($this));
    }
    return $this;
  }
}
