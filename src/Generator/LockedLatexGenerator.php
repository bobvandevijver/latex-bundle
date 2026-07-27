<?php

namespace Bobv\LatexBundle\Generator;

use Bobv\LatexBundle\Exception\LatexNotLockedException;
use Bobv\LatexBundle\Latex\LatexBaseInterface;
use DateTimeInterface;
use LogicException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Lock\Store\FlockStore;

/** Lock LaTeX PDF generation to prevent the same files being rendered at the same time. */
class LockedLatexGenerator implements LatexGeneratorInterface, LockedLatexGeneratorInterface
{
  private readonly LockFactory $lockFactory; // Set in constructor
  private ?LockInterface $lock    = null;
  private float|int|null $timeout = null; // Set in constructor

  public function __construct(private readonly LatexGeneratorInterface $generator)
  {
    if (!class_exists(LockFactory::class)) {
      throw new LogicException('In order to use the LockedLatexGenerator, try running "composer require symfony/lock".');
    }

    $this->setTimeout(60);
    $this->lockFactory = new LockFactory(new FlockStore());
  }

  /** Acquires a generation lock for PDF generation. This is required before using any method. */
  public function acquireLock(): LatexGeneratorInterface
  {
    if ($this->lock === null) {
      $this->lock = $this->lockFactory->createLock(self::class, $this->timeout);
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

  public function createPdfResponse(LatexBaseInterface $latex, bool $download = true, ?GeneratorOptions $options = null): BinaryFileResponse
  {
    $this->ensureLock();

    return $this->generator->createPdfResponse($latex, $download, $options);
  }

  public function createTexResponse(LatexBaseInterface $latex, bool $download = true, ?GeneratorOptions $options = null): BinaryFileResponse
  {
    $this->ensureLock();

    return $this->generator->createTexResponse($latex, $download, $options);
  }

  public function generate(LatexBaseInterface $latex, ?GeneratorOptions $options = null): string
  {
    $this->ensureLock();

    return $this->generator->generate($latex, $options);
  }

  public function generateLatex(LatexBaseInterface $latex, ?GeneratorOptions $options = null): string
  {
    $this->ensureLock();

    return $this->generator->generateLatex($latex, $options);
  }

  public function generatePdf(string $texLocation, ?GeneratorOptions $options = null): string
  {
    $this->ensureLock();

    return $this->generator->generatePdf($texLocation, $options);
  }

  public function setTimeout(float|int|null $timeout): self
  {
    $this->generator->setTimeout($timeout);

    if ($timeout === null) {
      // No timeout requested
      $this->timeout = null;
    } else {
      // Save timeout for lock, include some trivial overhead margin
      $this->timeout = $timeout + 5;
    }

    // Refresh lock when required
    if ($this->lock && $this->lock->isAcquired()) {
      $this->lock->refresh($this->timeout);
    }

    return $this;
  }

  /** Verify the callee has already exclusive access */
  private function ensureLock(): void
  {
    if (!$this->lock || !$this->lock->isAcquired()) {
      throw new LatexNotLockedException();
    }

    $this->lock->refresh($this->timeout);
  }
}
