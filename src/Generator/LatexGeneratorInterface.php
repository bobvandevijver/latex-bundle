<?php

namespace Bobv\LatexBundle\Generator;

use Bobv\LatexBundle\Exception\ImageNotFoundException;
use Bobv\LatexBundle\Exception\LatexException;
use Bobv\LatexBundle\Latex\LatexBaseInterface;
use DateTimeInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

interface LatexGeneratorInterface
{
  /**
   * Generate a response containing a PDF document.
   *
   * @param bool $download If set to false, the attachment value will be omitted from the
   *                       Content-Disposition field. Defaults to true.
   *
   * @throws ImageNotFoundException
   * @throws LatexException
   * @throws LoaderError
   * @throws RuntimeError
   * @throws SyntaxError
   */
  public function createPdfResponse(LatexBaseInterface $latex, bool $download = true, ?GeneratorOptions $options = null): BinaryFileResponse;

  /**
   * Generate a response containing a generated .tex file.
   *
   * @param bool $download If set to false, the attachment value will be omitted from the
   *                       Content-Disposition field. Defaults to true.
   *
   * @throws ImageNotFoundException
   * @throws LatexException
   * @throws LoaderError
   * @throws RuntimeError
   * @throws SyntaxError
   */
  public function createTexResponse(LatexBaseInterface $latex, bool $download = true, ?GeneratorOptions $options = null): BinaryFileResponse;

  /**
   * Compile a LaTeX object into the wanted PDF file.
   *
   * @throws LatexException
   * @throws LoaderError
   * @throws RuntimeError
   * @throws SyntaxError
   * @throws ImageNotFoundException
   *
   * @return string Location of the PDF document
   */
  public function generate(LatexBaseInterface $latex, ?GeneratorOptions $options = null): string;

  /**
   * Generates a latex file for the given LaTeX object.
   *
   * @param GeneratorOptions|null $options Optional options for the generator
   *
   * @throws LatexException
   * @throws LoaderError
   * @throws RuntimeError
   * @throws SyntaxError
   * @throws ImageNotFoundException
   *
   * @return string Location of the generated LaTeX file
   */
  public function generateLatex(LatexBaseInterface $latex, ?GeneratorOptions $options = null): string;

  /**
   * Generates a PDF from a given LaTeX location.
   *
   * @param string                $texLocation Location of the .tex file
   * @param GeneratorOptions|null $options     Optional options for the generator
   *
   * @throws LatexException
   * @throws IOException
   *
   * @return string Location of the generated PDF file
   */
  public function generatePdf(string $texLocation, ?GeneratorOptions $options = null): string;

  public function setMaxAge(DateTimeInterface $maxAge): self;

  /**
   * Sets the process timeout (max. runtime).
   * To disable the timeout, set this value to null.
   *
   * @param int|float|null $timeout The timeout in seconds
   */
  public function setTimeout(int|float|null $timeout): self;
}
