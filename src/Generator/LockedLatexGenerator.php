<?php

namespace Bobv\LatexBundle\Generator;

use Bobv\LatexBundle\Exception\LatexNotLockedException;
use Bobv\LatexBundle\Latex\LatexBaseInterface;
use DateTimeInterface;
use LogicException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Lock\Lock;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;

/**
 * Lock LaTeX pdf generation to prevent the same files being rendered at the same time.
 */
class LockedLatexGenerator implements LatexGeneratorInterface, LockedLatexGeneratorInterface
{
  private LockFactory $lockFactory; // Set in constructor
  private ?Lock $lock = null;
  private float|int|null $timeout; // Set in constructor

  public function __construct(private readonly LatexGeneratorInterface $generator) {
    if (!class_exists(LockFactory::class)) {
      throw new LogicException('In order to use the LockedLatexGenerator, try running "composer require symfony/lock".');
    }

    $this->setTimeout(60);
    $this->lockFactory = new LockFactory(new FlockStore());
  }

  /**
   * Acquires a generation lock for PDF generation. This is required before using any method.
   */
  public function acquireLock(): LatexGeneratorInterface {
    if ($this->lock === NULL) {
      $this->lock = $this->lockFactory->createLock(self::class, $this->timeout);
    }
    $this->lock->acquire(true);

    return $this;
  }

  /**
   * Release the lock when you're done with it
   * This is not needed for normal request flow.
   */
  public function releaseLock(): LatexGeneratorInterface {
    $this->lock->release();

    return $this;
  }

  public function createPdfResponse(LatexBaseInterface $latex, bool $download = true): BinaryFileResponse {
    $this->ensureLock();

    return $this->generator->createPdfResponse($latex, $download);
  }

  public function createTexResponse(LatexBaseInterface $latex, bool $download = true): BinaryFileResponse {
    $this->ensureLock();

    return $this->generator->createTexResponse($latex, $download);
  }

  public function generate(LatexBaseInterface $latex): string {
    $this->ensureLock();

    return $this->generator->generate($latex);
  }

  public function generateLatex(LatexBaseInterface $latex = null): string {
    $this->ensureLock();

    return $this->generator->generateLatex($latex);
  }

  public function generatePdf($texLocation, array $compileOptions = []): string {
    $this->ensureLock();

    return $this->generator->generatePdf($texLocation, $compileOptions);
  }

  public function setCacheDir(string $cacheDir): self {
    $this->generator->setCacheDir($cacheDir);

    return $this;
  }

  public function setForceRegenerate(bool $forceRegenerate): self {
    $this->generator->setForceRegenerate($forceRegenerate);

    return $this;
  }

  public function setMaxAge(DateTimeInterface $maxAge): self {
    $this->generator->setMaxAge($maxAge);

    return $this;
  }

  public function setTimeout(float|int|null $timeout): self {
    $this->generator->setTimeout($timeout);

    // Save timeout for lock, include some trivial overhead margin, and refresh when required
    $this->timeout = $timeout + 5;
    if ($this->lock && $this->lock->isAcquired()) {
      $this->lock->refresh($this->timeout);
    }

    return $this;
  }

  /**
   * Verify the callee has already exclusive access
   */
  private function ensureLock(): void {
    if (!$this->lock->isAcquired()) {
      throw new LatexNotLockedException();
    }

    $this->lock->refresh($this->timeout);
  }
}
