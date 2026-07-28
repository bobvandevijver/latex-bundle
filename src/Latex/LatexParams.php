<?php

namespace Bobv\LatexBundle\Latex;

use Bobv\LatexBundle\Exception\LatexException;

/** Use this to predefine the context methods and vars. */
class LatexParams
{
  /** @var array{packages?: array<array{p: string, o: string}>, ...} */
  protected array $params = [];

  /** @return array{packages?: array<array{p: string, o: string}>, ...} */
  public function getParams(): array
  {
    return $this->params;
  }

  /**
   * Set a specific parameter for the class.
   *
   * @throws LatexException
   */
  public function setParam(string $param, mixed $value): void
  {
    if (array_key_exists($param, $this->params)) {
      // Check if the param is defined as array
      if (is_array($this->params[$param])) {
        // If the value is an array, replace the complete array, else add a new array element
        if (is_array($value)) {
          /* @phpstan-ignore-next-line assign.propertyType */
          $this->params[$param] = $value;
        } else {
          /* @phpstan-ignore-next-line assign.propertyType */
          $this->params[$param][] = $value;
        }
      } else {
        /* @phpstan-ignore-next-line assign.propertyType */
        $this->params[$param] = $value;
      }
    } else {
      throw new LatexException("The param $param is not defined for " . static::class);
    }
  }
}
