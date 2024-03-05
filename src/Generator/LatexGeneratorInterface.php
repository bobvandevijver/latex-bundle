<?php

namespace Bobv\LatexBundle\Generator;

use Bobv\LatexBundle\Latex\LatexBaseInterface;
use DateTimeInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

interface LatexGeneratorInterface
{

  /**
   * Generate a response containing a PDF document
   *
   * @param bool               $download If set to false, the attachment value will be omitted from the
   *                                     Content-Disposition field. Defaults to true.
   */
  public function createPdfResponse(LatexBaseInterface $latex, bool $download = true): BinaryFileResponse;

  /**
   * Generate a response containing a generated .tex file
   *
   * @param bool               $download If set to false, the attachment value will be omitted from the
   *                                     Content-Disposition field. Defaults to true.
   */
  public function createTexResponse(LatexBaseInterface $latex, bool $download = true): BinaryFileResponse;

  /**
   * Compile a LaTeX object into the wanted PDF file
   *
   * @return string Location of the PDF document
   */
  public function generate(LatexBaseInterface $latex): string;

  /**
   * Generates a latex file for the given LaTeX object
   *
   * @return string Location of the generated LaTeX file
   */
  public function generateLatex(LatexBaseInterface $latex = null): string;

  /**
   * Generates a PDF from a given LaTeX location
   *
   * @param string $texLocation    Location of the .tex file
   * @param array  $compileOptions Optional compile options for pdflatex
   *
   * @return string Location of the generated PDF file
   */
  public function generatePdf(string $texLocation, array $compileOptions = []): string;

  public function setCacheDir(string $cacheDir): self;

  public function setForceRegenerate(bool $forceRegenerate): self;

  public function setMaxAge(DateTimeInterface $maxAge): self;

  /**
   * Sets the process timeout (max. runtime).
   * To disable the timeout, set this value to null.
   *
   * @param int|float|null $timeout The timeout in seconds
   */
  public function setTimeout(int|float|null $timeout): self;
}
