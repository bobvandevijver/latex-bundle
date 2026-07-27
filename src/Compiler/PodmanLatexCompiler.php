<?php

namespace Bobv\LatexBundle\Compiler;

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
  ) {
  }

  public function pdflatex(CompilerContext $context): LatexCompilerResult
  {
    return $this->runPodmanProcess($context, 'pdflatex', $context->pdflatexArgs);
  }

  public function bibtex(CompilerContext $context): LatexCompilerResult
  {
    return $this->runPodmanProcess($context, 'bibtex', [$context->baseName]);
  }

  public function supportsBibliography(): bool
  {
    return !empty($this->bibliographyBinaryLocation);
  }

  /** @param string[] $args */
  private function runPodmanProcess(CompilerContext $context, string $command, array $args): LatexCompilerResult
  {
    // We need the specific compiler context
    if (!$context->compilerContext instanceof PodmanCompilerContext) {
      $context->compilerContext = new PodmanCompilerContext($context);
    }

    $podmanArgs = [
      '--rm',
      '--read-only',
      '--tmpfs /tmp:rw,size=32m,noexec,nosuid',
      '--cap-drop ALL',
      '--security-opt no-new-privileges',
      '--memory 512m',
      '--pids-limit 20',
      sprintf('--workdir %s', $context->workingDirectory),
    ];

    $commandLine = sprintf(
      'HOME=%s %s run %s %s %s %s %s',
      $this->podmanHomeDir,
      $this->podmanBinaryPath,
      join(' ', $podmanArgs),
      $context->compilerContext->getVolumeBindsAsArgList(),
      $this->podmanImage,
      $command,
      join(' ', $args),
    );

    $process = Process::fromShellCommandline($commandLine);

    $process->setTimeout($this->timeout);
    $process->run();

    return LatexCompilerResult::fromProcess($process);
  }
}
