<?php

namespace Bobv\LatexBundle\Latex;

/**
 * Interface LatexInterface
 * All Latex objects should implement this class
 *
 * @package Bobv\LatexBundle\Latex
 */
interface LatexInterface
{

  /**
   * Needs to return the parameters for the twig render
   *
   * @return array
   */
  public function getContext();

  /**
   * Return all set params
   *
   * @return array
   */
  public function getParams();

  /**
   * Set a specific param in the context for Twig
   *
   * @param string $param
   * @param mixed $value
   *
   * @return LatexInterface
   */
  public function setParam($param, $value);

  /**
   * Should return the twig template
   *
   * @return string
   */
  public function getTemplate();

  /**
   * In case you want to change the twig template, use this method
   *
   * @param string $template
   *
   * @return LatexInterface
   */
  public function setTemplate($template);

}
