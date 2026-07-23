<?php

namespace Bobv\LatexBundle\Compiler;

use Symfony\Component\Process\Process;

final class LocalLatexCompiler implements LatexCompilerInterface
{
  public function __construct(
      protected string $pdfLatexBinaryLocation,
      protected ?string $bibliographyBinaryLocation,
  ) {
  }

  /** Default timeout from the Symfony Process component. */
  protected int|float|null $timeout = 60;

  public function pdflatex(string $workingDirectory, string $commandArguments): LatexCompilerResult
  {
    $command = $this->pdfLatexBinaryLocation . ' ' . $commandArguments;

    return LatexCompilerResult::fromProcess($this->runLocalProcess($workingDirectory, $command));
  }

  public function bibtex(string $workingDirectory, string $commandArguments): LatexCompilerResult
  {
    $command = $this->bibliographyBinaryLocation . ' ' . $commandArguments;

    return LatexCompilerResult::fromProcess($this->runLocalProcess($workingDirectory, $command));
  }

  public function supportsBibliography(): bool
  {
    return !empty($this->bibliographyBinaryLocation);
  }

  public function setTimeout(float|int|null $timeout): LatexCompilerInterface
  {
    $this->timeout = $timeout;

    return $this;
  }

  private function runLocalProcess(string $workingDirectory, string $command): Process
  {
    $commandLine = sprintf(
        'cd %s && HOME="/tmp" %s',
        $workingDirectory,
        $command,
    );

    $process = Process::fromShellCommandline($commandLine);

    $process->setTimeout($this->timeout);
    $process->run();

    return $process;
  }
}
