<?php

namespace Bobv\LatexBundle\Compiler;

use InvalidArgumentException;
use Symfony\Component\Process\Process;

final readonly class LatexCompilerResult
{
  /** @param string[] $output */
  public function __construct(
      public bool $isSuccessful,
      public array $output,
      public int $exitCode,
      public ?string $exitCodeText,
  )
  {
  }

  public static function fromProcess(Process $process): self
  {
    if (!$process->isTerminated()) {
      throw new InvalidArgumentException('Process must have finished');
    }

    return new self(
        $process->isSuccessful(),
        explode("\n", $process->getOutput()),
        $process->getExitCode(),
        $process->getExitCodeText(),
    );
  }
}
