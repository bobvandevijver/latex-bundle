<?php
namespace Bobv\LatexBundle\Latex\Element;

use Bobv\LatexBundle\Latex\LatexElement;

/**
 * @author BobV
 */
class BlackTitle extends LatexElement
{
  public function __construct(string $title)
  {
    $this->template = '@BobvLatex/Element/blacktitle.tex.twig';
    $this->params   = [
        'title'          => $title,
        'extra_commands' => [],
    ];
  }
}
