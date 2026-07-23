<?php

namespace Bobv\LatexBundle\Generator;

use Bobv\LatexBundle\Compiler\LatexCompilerInterface;
use Bobv\LatexBundle\Exception\BibliographyGenerationException;
use Bobv\LatexBundle\Exception\ImageNotFoundException;
use Bobv\LatexBundle\Exception\LatexException;
use Bobv\LatexBundle\Exception\LatexParseException;
use Bobv\LatexBundle\Latex\LatexBaseInterface;
use DateTime;
use DateTimeInterface;
use Exception;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class LatexGenerator implements LatexGeneratorInterface
{
  protected Filesystem $filesystem;
  protected bool $forceRegenerate      = false;
  protected ?LatexBaseInterface $latex = null;
  protected ?string $outputDir         = null;
  protected DateTimeInterface $maxAge;

  public function __construct(
    protected string $cacheDir,
    protected readonly string $env,
    protected readonly Environment $twig,
    protected LatexCompilerInterface $latexCompiler,
    string $maxAge,
  ) {
    $this->filesystem = new Filesystem();
    $this->maxAge     = new DateTime();
    $this->maxAge->modify($maxAge);
  }

  /**
   * Generate a response containing a PDF document.
   *
   * @throws ImageNotFoundException
   * @throws LatexException
   * @throws LoaderError
   * @throws RuntimeError
   * @throws SyntaxError
   */
  public function createPdfResponse(LatexBaseInterface $latex, bool $download = true): BinaryFileResponse
  {
    $pdfLocation = $this->generate($latex);

    $response = new BinaryFileResponse($pdfLocation);
    $response->headers->set('Content-Type', 'application/pdf;charset=utf-8');
    $response->headers->set('Content-Disposition',
      sprintf('%sfilename="%s.pdf"', $download ? 'attachment;' : '', $latex->getFileName()));

    return $response;
  }

  /**
   * Generate a response containing a generated .tex file.
   *
   * @throws ImageNotFoundException
   * @throws LatexException
   * @throws LoaderError
   * @throws RuntimeError
   * @throws SyntaxError
   */
  public function createTexResponse(LatexBaseInterface $latex, bool $download = true): BinaryFileResponse
  {
    $texLocation = $this->generateLatex($latex);

    $response = new BinaryFileResponse($texLocation);
    $response->headers->set('Content-Type', 'application/x-tex;charset=utf-8');
    $response->headers->set('Content-Disposition',
      sprintf('%sfilename="%s.tex"', $download ? 'attachment;' : '', $latex->getFileName()));

    return $response;
  }

  /**
   * Compile a LaTeX object into the wanted PDF file.
   *
   * @throws ImageNotFoundException
   * @throws LatexException
   * @throws LoaderError
   * @throws RuntimeError
   * @throws SyntaxError
   *
   * @return string Location of the PDF document
   */
  public function generate(LatexBaseInterface $latex): string
  {
    $this->latex = $latex;
    $texLocation = $this->generateLatex();

    return $this->generatePdf($texLocation);
  }

  /**
   * Generates a latex file for the given LaTeX object.
   *
   * @throws ImageNotFoundException
   * @throws LatexException
   * @throws LoaderError
   * @throws RuntimeError
   * @throws SyntaxError
   *
   * @return string Location of the generated LaTeX file
   */
  public function generateLatex(?LatexBaseInterface $latex = null): string
  {
    if ($this->latex === null && $latex === null) {
      throw new LatexException('No latex file given');
    }

    if ($latex === null) {
      $latex = $this->latex;
    }

    // Create the compiled tex-file
    $texData = $this->twig->render(
      $latex->getTemplate(),
      $latex->getContext()
    );

    $linkedDependencies = $latex->getLinkedDependencies();

    // Check if there are undefined images
    $matches = [];
    preg_match_all('/\\\\includegraphics(\[.+])?\{([^}]+)}/u', $texData, $matches);
    foreach ($matches[2] as $imageLocation) {
      if (array_key_exists($imageLocation, $linkedDependencies)) {
        continue;
      }

      if (!$this->filesystem->exists($imageLocation)) {
        throw new ImageNotFoundException($imageLocation);
      }
    }

    try {
      $texLocation = $this->writeTexFile($texData, $latex->getFileName());
    } catch (Exception $e) {
      if ($e instanceof IOException || $e instanceof LatexException) {
        throw $e;
      }
      throw new LatexException('Something failed during the creation of the tex file.', 0, $e);
    }

    // Copy dependencies to working dir
    foreach ($latex->getDependencies() as $dependency) {
      if ($this->filesystem->exists($dependency)) {
        $finder = new Finder();
        $finder->files()->in($dependency)->depth('== 0');
        foreach ($finder as $file) {
          /* @var SplFileInfo $file */
          $this->filesystem->copy($file->getRealpath(), $this->outputDir . $file->getFilename(), true);
        }
      }
    }

    // Link dependencies
    foreach ($linkedDependencies as $key => $dependency) {
      $this->filesystem->symlink($dependency, $this->outputDir . $key, true);
    }

    return $texLocation;
  }

  /**
   * Generates a PDF from a given LaTeX location.
   *
   * @param string $texLocation    Location of the .tex file
   * @param array  $compileOptions Optional compile options for pdflatex
   *
   * @throws IOException
   * @throws LatexException
   *
   * @return string Location of the generated PDF file
   */
  public function generatePdf(string $texLocation, array $compileOptions = []): string
  {
    // Check if the compiled tex file exists
    if (!$this->filesystem->exists($texLocation)) {
      throw new IOException("The LaTeX file at $texLocation does not exist. Is the cache writable or did you forget to generate the .tex file?");
    }

    try {
      $pdfLocation = $this->compilePdf($texLocation, $compileOptions);
    } catch (Exception $e) {
      if ($e instanceof IOException || $e instanceof LatexException) {
        throw $e;
      }

      if ($e instanceof ProcessTimedOutException) {
        throw new LatexException('The execution of the pdflatex command timed out. Is it a extremely large file?', 0, $e);
      }

      throw new LatexException('Something failed during the compilation of the pdf file. Check the logs for more info. (filename: ' . explode('.tex', $texLocation)[0] . '.log )', 0, $e);
    }

    return $pdfLocation;
  }

  public function setCacheDir(string $cacheDir): self
  {
    $this->cacheDir = $cacheDir;

    return $this;
  }

  public function setForceRegenerate(bool $forceRegenerate): self
  {
    $this->forceRegenerate = $forceRegenerate;

    return $this;
  }

  public function setMaxAge(DateTimeInterface $maxAge): self
  {
    $this->maxAge = $maxAge;

    return $this;
  }

  /**
   * Sets the process timeout (max. runtime).
   * To disable the timeout, set this value to null.
   *
   * @param int|float|null $timeout The timeout in seconds
   */
  public function setTimeout(int|float|null $timeout): self
  {
    $this->latexCompiler->setTimeout($timeout);

    return $this;
  }

  /**
   * Checks if the filesystem is ready to create the needed files.
   *
   * @throws IOException
   */
  protected function checkFilesystem(): void
  {
    // Check if the cache dir exists
    if (!$this->filesystem->exists($this->getCacheBasePath())) {
      try {
        $this->filesystem->mkdir($this->getCacheBasePath());
      } catch (IOException $ioe) {
        throw new IOException('An error occurred while creating the cache directory at ' . $ioe->getPath() . '. Is the cache writable?', 0, $ioe);
      }
    }
  }

  /**
   * Compile a PDF from a tex file on a given location
   * If necessary, compilation will be done up to three times for correct references.
   *
   * @param string $texLocation    Location of the .tex file
   * @param array  $compileOptions Optional compile options for pdflatex
   *
   * @throws Exception
   */
  protected function compilePdf(string $texLocation, array $compileOptions = []): string
  {
    $baseName    = explode('.tex', $texLocation)[0];
    $pdfLocation = $baseName . '.pdf';

    // Do not regenerate unless cache is off (dev mode or force regenerate or passed maxAge)
    if ($this->filesystem->exists($pdfLocation)
        && $this->env == 'prod'
        && filemtime($pdfLocation) > $this->maxAge->getTimestamp()
        && !$this->forceRegenerate
    ) {
      return $pdfLocation;
    }

    // Get the output directory from the tex file
    $this->outputDir = dirname($texLocation);

    // Process extra options for security
    $pdflatexArgs = '';
    foreach ($compileOptions as $option => $value) {
      $pdflatexArgs .= sprintf(' -%s %s', $option, $value ?: '');
    }

    // Add -no-shell-escape
    if (!array_key_exists('shell-escape', $compileOptions)) {
      $pdflatexArgs .= ' -no-shell-escape';
    }

    // Add -openin_any=p and -openout_any=p
    if (!array_key_exists('openin_any', $compileOptions)) {
      $pdflatexArgs .= ' -cnf-line="openin_any=p"';
    }
    if (!array_key_exists('openout_any', $compileOptions)) {
      $pdflatexArgs .= ' -cnf-line="openout_any=p"';
    }

    // Always add interaction mode
    $pdflatexArgs .= ' -interaction=nonstopmode';

    // Always add output directory
    $pdflatexArgs .= sprintf(' -output-directory="%s"', $this->outputDir);

    // Finally, add the tex location
    $pdflatexArgs .= sprintf(' "%s"', $texLocation);

    $compile = true;
    $count   = 0;
    /** @var bool|array $output */
    $output = false;

    // Compile until all references are solved or three times is reached
    while ($compile && $count < 3) {
      // Always add one pass for when a large TOC is present, but do not check before the first run
      if ($output !== false && count(array_filter($output, $this->findReferenceError(...))) == 0) {
        $compile = false;
      }
      unset($output);

      $result = $this->latexCompiler->pdflatex($this->outputDir, $pdflatexArgs);
      $output = $result->output;

      // Check if the pdflatex command completed successfully
      if (!$result->isSuccessful) {
        throw new LatexParseException(
          $texLocation,
          $result->exitCode,
          $output,
          $result->exitCodeText,
        );
      }

      // Run bibliography tooling when enabled
      if ($count === 0 && $this->latexCompiler->supportsBibliography()) {
        $result = $this->latexCompiler->bibtex($this->outputDir, $baseName);

        // Check whether the pdflatex command completed successfully
        if (!$result->isSuccessful) {
          throw new BibliographyGenerationException(
            $texLocation,
            $result->exitCode,
            $result->exitCodeText,
          );
        }
      }

      $count++;
    }

    return $pdfLocation;
  }

  protected function getCacheBasePath(): string
  {
    return $this->cacheDir . '/BobvLatex/';
  }

  /**
   * Write the file to the cache directory.
   *
   * @throws IOException
   *
   * @return string The location of the saved .tex file
   */
  protected function writeTexFile(string $texData, string $fileName): string
  {
    $this->checkFilesystem();
    $this->outputDir = $this->getCacheBasePath() . hash('ripemd160', $texData) . '/';
    $texLocation     = $this->outputDir . $fileName . '.tex';
    try {
      // Do not regenerate unless cache is off (dev mode or force regenerate or passed maxAge)
      if ($this->filesystem->exists($texLocation)
          && $this->env == 'prod'
          && filemtime($texLocation) > $this->maxAge->getTimestamp()
          && !$this->forceRegenerate
      ) {
        return $texLocation;
      }

      $this->filesystem->dumpFile(
        $texLocation,
        $texData
      );
    } catch (IOException $ioe) {
      throw new IOException('An error occurred while creating the tex file at ' . $ioe->getPath() . '. Is the cache writable?', 0, $ioe);
    }

    return $texLocation;
  }

  /** Check if the line contains a reference error */
  private function findReferenceError(string $value): bool
  {
    return preg_match_all('/reference|change/ui', $value) > 0;
  }
}
