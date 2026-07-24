<?php

namespace Bobv\LatexBundle\Compiler;

use Symfony\Component\Process\Process;

/** Simple local LaTeX compiler, which uses a locally installed LaTeX binary. */
final class LocalLatexCompiler extends AbstractTimeoutCompiler
{
  public function __construct(
    private readonly string $pdfLatexBinaryLocation,
    private readonly ?string $bibliographyBinaryLocation,
  ) {
  }

  public function pdflatex(CompilerContext $context): LatexCompilerResult
  {
    $command = $this->pdfLatexBinaryLocation . ' ' . $context->pdflatexArgs;

    return $this->runLocalProcess($context, $command);
  }

  public function bibtex(CompilerContext $context): LatexCompilerResult
  {
    $command = $this->bibliographyBinaryLocation . ' ' . $context->baseName;

    return $this->runLocalProcess($context, $command);
  }

  public function supportsBibliography(): bool
  {
    return !empty($this->bibliographyBinaryLocation);
  }

  private function runLocalProcess(CompilerContext $context, string $command): LatexCompilerResult
  {
    $commandLine = sprintf(
      'cd %s && HOME="/tmp" %s',
      $context->workingDirectory,
      $command,
    );

    $process = Process::fromShellCommandline($commandLine);

    $process->setTimeout($this->timeout);
    $process->run();

    return LatexCompilerResult::fromProcess($process);
  }
}
