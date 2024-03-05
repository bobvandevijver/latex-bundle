<?php

namespace Bobv\LatexBundle\Generator;

/**
 * Lock LaTeX pdf generation to prevent the same files being rendered at the same time.
 */
interface LockedLatexGeneratorInterface extends LatexGeneratorInterface
{
  /**
   * Acquires a generation lock for PDF generation. This is required before using any method.
   */
  public function acquireLock(): LatexGeneratorInterface;

  /**
   * Release the lock when you're done with it
   * This is not needed for normal request flow.
   */
  public function releaseLock(): LatexGeneratorInterface;
}
