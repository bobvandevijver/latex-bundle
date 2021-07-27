<?php

namespace BobV\LatexBundle\Generator;

use BobV\LatexBundle\Exception\LatexNotLockedException;
use BobV\LatexBundle\Latex\LatexBaseInterface;
use LogicException;
use Symfony\Component\Lock\Lock;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;

/**
 * Class LockedLatexGenerator
 *
 * Lock LaTeX pdf generation to prevent the same files being rendered at the same time.
 */
class LockedLatexGenerator implements LatexGeneratorInterface, LockedLatexGeneratorInterface
{
  /**
   * @var LatexGeneratorInterface
   */
  private $generator;
  /**
   * @var LockFactory
   */
  private $lockFactory;
  /**
   * @var Lock|null
   */
  private $lock;
  /**
   * @var int
   */
  private $timeout;

  public function __construct(LatexGeneratorInterface $generator) {
    if (!class_exists(LockFactory::class)) {
      throw new LogicException('In order to use the LockedLatexGenerator, try running "composer require symfony/lock".');
    }

    $this->generator = $generator;
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

  /**
   * @inheritDoc
   */
  public function createPdfResponse(LatexBaseInterface $latex, bool $download = true) {
    $this->ensureLock();

    return $this->generator->createPdfResponse($latex, $download);
  }

  /**
   * @inheritDoc
   */
  public function createTexResponse(LatexBaseInterface $latex, bool $download = true) {
    $this->ensureLock();

    return $this->generator->createTexResponse($latex, $download);
  }

  /**
   * @inheritDoc
   */
  public function generate(LatexBaseInterface $latex) {
    $this->ensureLock();

    return $this->generator->generate($latex);
  }

  /**
   * @inheritDoc
   */
  public function generateLatex(LatexBaseInterface $latex = NULL) {
    $this->ensureLock();

    return $this->generator->generateLatex($latex);
  }

  /**
   * @inheritDoc
   */
  public function generatePdf($texLocation, array $compileOptions = array()) {
    $this->ensureLock();

    return $this->generator->generatePdf($texLocation, $compileOptions);
  }

  /**
   * @inheritDoc
   */
  public function setCacheDir($cacheDir) {
    $this->generator->setCacheDir($cacheDir);

    return $this;
  }

  /**
   * @inheritDoc
   */
  public function setForceRegenerate($forceRegenerate) {
    $this->generator->setForceRegenerate($forceRegenerate);

    return $this;
  }

  /**
   * @inheritDoc
   */
  public function setMaxAge($maxAge) {
    $this->generator->setMaxAge($maxAge);

    return $this;
  }

  /**
   * @inheritDoc
   */
  public function setTimeout($timeout) {
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
  private function ensureLock() {
    if (!$this->lock->isAcquired()) {
      throw new LatexNotLockedException();
    }

    $this->lock->refresh($this->timeout);
  }
}
