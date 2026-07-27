<?php

namespace Bobv\LatexBundle\Compiler;

use Symfony\Component\Finder\Finder;

final readonly class PodmanCompilerContext
{
  /** @var array<string, string> */
  private array $volumeBinds;

  public function __construct(CompilerContext $context)
  {
    // We need to bind all relevant directories to the container.
    // Those are the current working directly, but also any symlinked file origins so that they are accessible.

    $binds = [
      $context->workingDirectory => $context->workingDirectory,
    ];

    // Determine the symlinks
    foreach ((new Finder())->in($context->workingDirectory) as $fileOrDir) {
      if (!$fileOrDir->isLink()) {
        continue;
      }

      $binds[$fileOrDir->getRealPath()] = $fileOrDir->getRealPath();
    }

    $this->volumeBinds = $binds;
  }

  public function getVolumeBindsAsArgList(): string
  {
    $args = [];

    foreach ($this->volumeBinds as $hostPath => $containerPath) {
      $args[] = sprintf('-v %s:%s', $hostPath, $containerPath);
    }

    return join(' ', $args);
  }

  /** @return string[] Array of binds, in the format "src:target" */
  public function getVolumeBinds(): array
  {
    $volumeBinds = [];

    foreach ($this->volumeBinds as $hostPath => $containerPath) {
      $volumeBinds[] = sprintf('%s:%s', $hostPath, $containerPath);
    }

    return $volumeBinds;
  }
}
