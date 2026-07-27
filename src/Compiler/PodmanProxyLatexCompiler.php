<?php

namespace Bobv\LatexBundle\Compiler;

use Bobv\LatexBundle\Exception\LatexProxyException;
use JsonException;
use Symfony\Component\Process\Process;
use Throwable;

/**
 * LaTeX compiler which uses podman to run a LaTeX container.
 *
 * Note that this will not work by default using Debian (Sury) and PHP-FPM,
 * as the PHP-FPM service is not allowed to create namespaces.
 */
final readonly class PodmanProxyLatexCompiler implements LatexCompilerInterface
{
  public function __construct(private string $podmanProxySock)
  {
  }

  public function pdflatex(CompilerContext $context): LatexCompilerResult
  {
    return $this->runPodmanProcess($context, 'pdflatex', [
      ...$context->pdflatexArgs,
      "$context->baseName.tex",
    ]);
  }

  public function bibtex(CompilerContext $context): LatexCompilerResult
  {
    return $this->runPodmanProcess($context, 'bibtex', [$context->baseName]);
  }

  public function supportsBibliography(): bool
  {
    return true;
  }

  /**
   * @param string[] $args
   *
   * @throws LatexProxyException
   */
  private function runPodmanProcess(CompilerContext $context, string $command, array $args): LatexCompilerResult
  {
    // We need the specific compiler context
    if (!$context->compilerContext instanceof PodmanCompilerContext) {
      $context->compilerContext = new PodmanCompilerContext($context);
    }

    try {
      $request = json_encode([
        'working_directory' => $context->workingDirectory,
        'volumes'           => $context->compilerContext->getVolumeBinds(),
        'command'           => $command,
        'args'              => $args,
      ], JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
      throw new LatexProxyException('Failed to create podman proxy payload', previous: $e);
    }

    try {
      $fp = fsockopen($this->podmanProxySock, -1, $errorCode, $errorMessage, $context->timeout);
      if (!$fp) {
        throw new LatexProxyException('Failed to connect to podman proxy service');
      }
    } catch (Throwable $e) {
      throw new LatexProxyException('Failed to connect to podman proxy service', previous: $e);
    }

    fwrite($fp, $request);
    stream_socket_shutdown($fp, STREAM_SHUT_WR);
    $response = stream_get_contents($fp);
    fclose($fp);
    try {
      $data = json_decode($response, true, flags: JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
      throw new LatexProxyException('Failed to decode podman proxy response', previous: $e);
    }

    $exitCode = (int)$data['status'];

    return new LatexCompilerResult(
      $exitCode === 0,
      $exitCode,
      $data['stdout'] ? explode("\n", $data['stdout']) : [],
      Process::$exitCodes[$exitCode] ?? null,
      $data['stderr'] ? explode("\n", $data['stderr']) : [],
    );
  }
}
