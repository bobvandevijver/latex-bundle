<?php

namespace Bobv\LatexBundle\Generator;

use Bobv\LatexBundle\Exception\ImageNotFoundException;
use Bobv\LatexBundle\Exception\LatexException;
use Bobv\LatexBundle\Latex\LatexBaseInterface;
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
  public function createPdfResponse(
    LatexBaseInterface $latex,
    bool $download = true,
    ?GeneratorOptions $options = null,
  ): BinaryFileResponse;

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
  public function createTexResponse(
    LatexBaseInterface $latex,
    bool $download = true,
    ?GeneratorOptions $options = null,
  ): BinaryFileResponse;

  /**
   * Compile a LaTeX object into the wanted PDF file.
   *
   * @throws LoaderError
   * @throws RuntimeError
   * @throws SyntaxError
   * @throws ImageNotFoundException
   * @throws LatexException
   *
   * @return PdfFileInfo Location of the generated PDF document
   */
  public function generate(LatexBaseInterface $latex, ?GeneratorOptions $options = null): PdfFileInfo;

  /**
   * Generates a latex file for the given LaTeX object.
   *
   * @param GeneratorOptions|null $options Optional options for the generator
   *
   * @throws LoaderError
   * @throws RuntimeError
   * @throws SyntaxError
   * @throws ImageNotFoundException
   * @throws LatexException
   *
   * @return TexFileInfo Location of the generated LaTeX file
   */
  public function generateLatex(LatexBaseInterface $latex, ?GeneratorOptions $options = null): TexFileInfo;

  /**
   * Generates a PDF from a given LaTeX location.
   *
   * @param TexFileInfo           $texFileInfo Information about the LaTeX (.tex) file
   * @param GeneratorOptions|null $options     Optional options for the generator
   *
   * @throws IOException
   * @throws LatexException
   *
   * @return PdfFileInfo Location of the generated PDF file
   */
  public function generatePdf(TexFileInfo $texFileInfo, ?GeneratorOptions $options = null): PdfFileInfo;
}
