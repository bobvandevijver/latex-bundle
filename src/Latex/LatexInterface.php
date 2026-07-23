<?php

namespace Bobv\LatexBundle\Latex;

/** All Latex objects should implement this class */
interface LatexInterface
{
  /**
   * Needs to return the parameters for the twig render.
   *
   * @return array<string, mixed>
   */
  public function getContext(): array;

  /**
   * Return all set params.
   *
   * @return array<string, mixed>
   */
  public function getParams(): array;

  /** Set a specific param in the context for Twig */
  public function setParam(string $param, mixed $value): void;

  /** Should return the twig template */
  public function getTemplate(): string;

  /** In case you want to change the twig template, use this method */
  public function setTemplate(string $template): self;
}
