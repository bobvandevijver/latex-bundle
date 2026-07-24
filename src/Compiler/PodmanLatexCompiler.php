<?php

namespace Bobv\LatexBundle\Compiler;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

/**
 * LaTeX compiler which uses podman to run a LaTeX container.
 *
 * Note that this will not work by default using Debian (Sury) and PHP-FPM,
 * as the PHP-FPM service is not allowed to create namespaces.
 */
final class PodmanLatexCompiler extends AbstractTimeoutCompiler
{
  public function __construct(
    private readonly string $podmanHomeDir,
    private readonly string $podmanBinaryPath,
    private readonly string $podmanImage,
    private readonly string $pdfLatexBinaryLocation,
    private readonly ?string $bibliographyBinaryLocation,
  ) {
  }

  public function pdflatex(CompilerContext $context): LatexCompilerResult
  {
    $containerCommand = $this->pdfLatexBinaryLocation . ' ' . $context->pdflatexArgs;

    return $this->runPodmanProcess($context, $containerCommand);
  }

  public function bibtex(CompilerContext $context): LatexCompilerResult
  {
    $containerCommand = $this->bibliographyBinaryLocation . ' ' . $context->baseName;

    return $this->runPodmanProcess($context, $containerCommand);
  }

  public function supportsBibliography(): bool
  {
    return !empty($this->bibliographyBinaryLocation);
  }

  private function runPodmanProcess(CompilerContext $context, string $command): LatexCompilerResult
  {
    // We need to bind all relevant directories to the container.
    // Those are the current working directly, but also any symlinked file origins so that they are accessible.
    if (!$context->compilerContext instanceof PodmanCompilerContext) {
      $binds = [
        $context->workingDirectory => $context->workingDirectory,
      ];

      // Determine the symlinks
      $fs = new Filesystem();
      foreach ((new Finder())->in($context->workingDirectory) as $fileOrDir) {
        if (!$fileOrDir->isLink()) {
          continue;
        }

        $binds[$fileOrDir->getRealPath()] = $fileOrDir->getRealPath();
      }

      $context->compilerContext = new PodmanCompilerContext($binds);
    }

    $podmanArgs = [
      '--read-only',
      '--tmpfs /tmp:rw,size=32m,noexec,nosuid',
      '--cap-drop=ALL',
      '--security-opt no-new-privileges',
      '--memory=512m',
      '--pids-limit=20',
      '--cgroup-manager=cgroupfs',
      sprintf('--workdir %s', $context->workingDirectory),
    ];

    $commandLine = sprintf(
      'HOME=%s %s run --rm %s %s %s %s',
      $this->podmanHomeDir,
      $this->podmanBinaryPath,
      join(' ', $podmanArgs),
      $context->compilerContext->getBindArgs(),
      $this->podmanImage,
      $command,
    );

    $process = Process::fromShellCommandline($commandLine);

    $process->setTimeout($this->timeout);
    $process->run();

    return LatexCompilerResult::fromProcess($process);
  }
}
