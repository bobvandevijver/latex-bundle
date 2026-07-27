<?php

namespace Bobv\LatexBundle\Compiler;

use InvalidArgumentException;
use Symfony\Component\Process\Process;

final readonly class LatexCompilerResult
{
  /**
   * @param list<string> $output
   * @param list<string> $errorOutput
   */
  public function __construct(
    public bool $isSuccessful,
    public int $exitCode,
    public array $output,
    public ?string $exitCodeText,
    public array $errorOutput,
  ) {
  }

  public static function fromProcess(Process $process): self
  {
    if (!$process->isTerminated() || null === $exitCode = $process->getExitCode()) {
      throw new InvalidArgumentException('Process must have finished');
    }

    return new self(
      $process->isSuccessful(),
      $exitCode,
      explode("\n", $process->getOutput()),
      $process->getExitCodeText(),
      explode("\n", $process->getErrorOutput()),
    );
  }
}
