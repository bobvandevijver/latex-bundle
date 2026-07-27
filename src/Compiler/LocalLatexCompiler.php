<?php

namespace Bobv\LatexBundle\Compiler;

use Symfony\Component\Process\Process;

/** Simple local LaTeX compiler, which uses a locally installed LaTeX binary. */
final readonly class LocalLatexCompiler implements LatexCompilerInterface
{
  public function __construct(
    private string $pdfLatexBinaryLocation,
    private string $bibliographyBinaryLocation,
  ) {
  }

  public function pdflatex(CompilerContext $context): LatexCompilerResult
  {
    return $this->runLocalProcess($context, $this->pdfLatexBinaryLocation, [
      ...$context->pdflatexArgs,
      "\"$context->baseName.tex\"",
    ]);
  }

  public function bibtex(CompilerContext $context): LatexCompilerResult
  {
    return $this->runLocalProcess($context, $this->bibliographyBinaryLocation, [$context->baseName]);
  }

  public function supportsBibliography(): bool
  {
    return !empty($this->bibliographyBinaryLocation);
  }

  /** @param string[] $args */
  private function runLocalProcess(CompilerContext $context, string $binaryLocation, array $args): LatexCompilerResult
  {
    $commandLine = sprintf(
      'cd %s && HOME="/tmp" %s %s',
      $context->workingDirectory,
      $binaryLocation,
      join(' ', $args),
    );

    $process = Process::fromShellCommandline($commandLine);

    $process->setTimeout($context->timeout);
    $process->run();

    return LatexCompilerResult::fromProcess($process);
  }
}
