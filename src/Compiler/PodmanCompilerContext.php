<?php

namespace Bobv\LatexBundle\Compiler;

final readonly class PodmanCompilerContext
{
  /** @param array<string, string> $volumeBinds */
  public function __construct(private array $volumeBinds)
  {
  }

  public function getBindArgs(): string
  {
    $args = [];

    foreach ($this->volumeBinds as $hostPath => $containerPath) {
      $args[] = sprintf('-v %s:%s', $hostPath, $containerPath);
    }

    return join(' ', $args);
  }
}
