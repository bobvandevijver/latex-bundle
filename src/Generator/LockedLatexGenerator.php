<?php

namespace Bobv\LatexBundle\Generator;

use Bobv\LatexBundle\Exception\LatexNotLockedException;
use Bobv\LatexBundle\Latex\LatexBaseInterface;
use LogicException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Lock\Store\FlockStore;

/** Lock LaTeX PDF generation to prevent the same files being rendered at the same time. */
class LockedLatexGenerator implements LatexGeneratorInterface, LockedLatexGeneratorInterface
{
  /** Define a trivial overhead for the timeout margin to prevent releasing the lock before the process timeout has happened. */
  private const TIMEOUT_OVERHEAD = 2;
  private readonly LockFactory $lockFactory; // Set in constructor
  private ?LockInterface       $lock = null;

  public function __construct(private readonly LatexGeneratorInterface $generator)
  {
    if (!class_exists(LockFactory::class)) {
      throw new LogicException('In order to use the LockedLatexGenerator, try running "composer require symfony/lock".');
    }

    $this->lockFactory = new LockFactory(new FlockStore());
  }

  /** Acquires a generation lock for PDF generation. This is required before using any method. */
  public function acquireLock(?GeneratorOptions $options = null): LatexGeneratorInterface
  {
    if ($this->lock === null) {
      $this->lock = $this->lockFactory->createLock(self::class, $this->getTimeout($options));
    }
    $this->lock->acquire(true);

    return $this;
  }

  /**
   * Release the lock when you're done with it
   * This is not needed for normal request flow.
   */
  public function releaseLock(): LatexGeneratorInterface
  {
    $this->lock?->release();

    return $this;
  }

  public function createPdfResponse(
    LatexBaseInterface $latex,
    bool $download = true,
    ?GeneratorOptions $options = null,
  ): BinaryFileResponse
  {
    $this->ensureLock($options);

    return $this->generator->createPdfResponse($latex, $download, $options);
  }

  public function createTexResponse(
    LatexBaseInterface $latex,
    bool $download = true,
    ?GeneratorOptions $options = null,
  ): BinaryFileResponse
  {
    $this->ensureLock($options);

    return $this->generator->createTexResponse($latex, $download, $options);
  }

  public function generate(LatexBaseInterface $latex, ?GeneratorOptions $options = null): string
  {
    $this->ensureLock($options);

    return $this->generator->generate($latex, $options);
  }

  public function generateLatex(LatexBaseInterface $latex, ?GeneratorOptions $options = null): string
  {
    $this->ensureLock($options);

    return $this->generator->generateLatex($latex, $options);
  }

  public function generatePdf(string $texLocation, ?GeneratorOptions $options = null): string
  {
    $this->ensureLock($options);

    return $this->generator->generatePdf($texLocation, $options);
  }

  /** Verify the callee already has exclusive access */
  private function ensureLock(?GeneratorOptions $options): void
  {
    if (!$this->lock || !$this->lock->isAcquired()) {
      throw new LatexNotLockedException();
    }

    $this->lock->refresh($this->getTimeout($options));
  }

  private function getTimeout(?GeneratorOptions $options): float|int|null
  {
    return ($options?->timeout ?? GeneratorOptions::DEFAULT_TIMEOUT) + self::TIMEOUT_OVERHEAD;
  }
}
