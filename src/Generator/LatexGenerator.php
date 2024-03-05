<?php

namespace Bobv\LatexBundle\Generator;

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
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class LatexGenerator implements LatexGeneratorInterface
{
  protected Filesystem $filesystem;
  protected bool $forceRegenerate = false;
  protected ?LatexBaseInterface $latex = null;
  protected ?string $outputDir = null;
  protected int|float|null $timeout = 60; // Default timeout from the Symfony Process component
  protected DateTimeInterface $maxAge;

  public function __construct(
      protected string $cacheDir,
      protected readonly string $env,
      protected readonly Environment $twig,
      string $maxAge,
      protected readonly string $pdfLatexBinaryLocation,
      protected readonly string $bibliographyBinaryLocation) {
    $this->filesystem = new Filesystem();
    $this->maxAge     = new DateTime();
    $this->maxAge->modify($maxAge);
  }

  /**
   * Generate a response containing a PDF document
   *
   * @throws ImageNotFoundException
   * @throws LatexException
   * @throws LoaderError
   * @throws RuntimeError
   * @throws SyntaxError
   */
  public function createPdfResponse(LatexBaseInterface $latex, bool $download = true): BinaryFileResponse {
    $pdfLocation = $this->generate($latex);

    $response = new BinaryFileResponse($pdfLocation);
    $response->headers->set('Content-Type', 'application/pdf;charset=utf-8');
    $response->headers->set('Content-Disposition',
        sprintf('%sfilename="%s.pdf"', $download ? 'attachment;' : '', $latex->getFileName()));

    return $response;
  }

  /**
   * Generate a response containing a generated .tex file
   *
   * @throws ImageNotFoundException
   * @throws LatexException
   * @throws LoaderError
   * @throws RuntimeError
   * @throws SyntaxError
   */
  public function createTexResponse(LatexBaseInterface $latex, bool $download = true): BinaryFileResponse {
    $texLocation = $this->generateLatex($latex);

    $response = new BinaryFileResponse($texLocation);
    $response->headers->set('Content-Type', 'application/x-tex;charset=utf-8');
    $response->headers->set('Content-Disposition',
        sprintf('%sfilename="%s.tex"', $download ? 'attachment;' : '', $latex->getFileName()));

    return $response;
  }

  /**
   * Compile a LaTeX object into the wanted PDF file
   *
   * @return string Location of the PDF document
   *
   * @throws ImageNotFoundException
   * @throws LatexException
   * @throws LoaderError
   * @throws RuntimeError
   * @throws SyntaxError
   */
  public function generate(LatexBaseInterface $latex): string {
    $this->latex = $latex;
    $texLocation = $this->generateLatex();

    return $this->generatePdf($texLocation);
  }

  /**
   * Generates a latex file for the given LaTeX object
   *
   * @return string Location of the generated LaTeX file
   *
   * @throws ImageNotFoundException
   * @throws LatexException
   * @throws LoaderError
   * @throws RuntimeError
   * @throws SyntaxError
   */
  public function generateLatex(LatexBaseInterface $latex = NULL): string {

    if ($this->latex === NULL && $latex === NULL) {
      throw new LatexException("No latex file given");
    }

    if ($latex === NULL) {
      $latex = $this->latex;
    }

    // Create the compiled tex-file
    $texData = $this->twig->render(
        $latex->getTemplate(),
        $latex->getContext()
    );

    // Check if there are undefined images
    $matches = array();
    preg_match_all('/\\\\includegraphics(\[.+])?\{([^}]+)}/u', $texData, $matches);
    foreach ($matches[2] as $imageLocation) {
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
      throw new LatexException("Something failed during the creation of the tex file.", 0, $e);
    }

    // Copy dependencies to working dir
    foreach ($latex->getDependencies() as $dependency) {
      if ($this->filesystem->exists($dependency)) {
        $finder = new Finder();
        $finder->files()->in($dependency)->depth('== 0');
        foreach ($finder as $file) {
          /** @var SplFileInfo $file */
          $this->filesystem->copy($file->getRealpath(), $this->outputDir . $file->getFilename(), true);
        }
      }
    }

    return $texLocation;

  }

  /**
   * Generates a PDF from a given LaTeX location
   *
   * @param string $texLocation    Location of the .tex file
   * @param array  $compileOptions Optional compile options for pdflatex
   *
   * @return string Location of the generated PDF file
   * @throws IOException
   * @throws LatexException
   */
  public function generatePdf(string $texLocation, array $compileOptions = []): string {

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
        throw new LatexException("The execution of the pdflatex command timed out. Is it a extremely large file?", 0, $e);
      }

      throw new LatexException("Something failed during the compilation of the pdf file. Check the logs for more info. (filename: " . explode('.tex', $texLocation)[0] . ".log )", 0, $e);
    }

    return $pdfLocation;

  }

  public function setCacheDir(string $cacheDir): self {
    $this->cacheDir = $cacheDir;

    return $this;
  }

  public function setForceRegenerate(bool $forceRegenerate): self {
    $this->forceRegenerate = $forceRegenerate;

    return $this;
  }

  public function setMaxAge(DateTimeInterface $maxAge): self {
    $this->maxAge = $maxAge;

    return $this;
  }

  /**
   * Sets the process timeout (max. runtime).
   * To disable the timeout, set this value to null.
   *
   * @param int|float|null $timeout The timeout in seconds
   */
  public function setTimeout(int|float|null $timeout): self {
    $this->timeout = $timeout;

    return $this;
  }

  /**
   * Checks if the filesystem is ready to create the needed files
   *
   * @throws IOException
   */
  protected function checkFilesystem(): void {
    // Check if the cache dir exists
    if (!$this->filesystem->exists($this->getCacheBasePath())) {
      try {
        $this->filesystem->mkdir($this->getCacheBasePath());
      } catch (IOException $ioe) {
        throw new IOException("An error occurred while creating the cache directory at " . $ioe->getPath() . ". Is the cache writable?", 0, $ioe);
      }
    }
  }

  /**
   * Compile a PDF from a tex file on a given location
   * If necessary, compilation will be done up to three times for correct references
   *
   * @param string $texLocation    Location of the .tex file
   * @param array  $compileOptions Optional compile options for pdflatex
   *
   * @throws Exception
   */
  protected function compilePdf(string $texLocation, array $compileOptions = []): string {
    $pdfLocation = explode('.tex', $texLocation)[0] . '.pdf';

    // Do not regenerate unless cache is off (dev mode or force regenerate or passed maxAge)
    if ($this->filesystem->exists($pdfLocation)
        && $this->env == "prod"
        && filemtime($pdfLocation) > $this->maxAge->getTimestamp()
        && !$this->forceRegenerate
    ) {
      return $pdfLocation;
    }

    // Get the output directory from the tex file
    $this->outputDir = dirname($texLocation);

    // Process extra options for security
    $optionsString = '';
    foreach ($compileOptions as $option => $value) {
      $optionsString .= sprintf(' -%s %s', $option, $value ? $value : '');
    }

    // Add -no-shell-escape
    if (!array_key_exists('shell-escape', $compileOptions)) {
      $optionsString .= ' -no-shell-escape';
    }

    $compile = true;
    $count   = 0;
    /** @var bool|array $output */
    $output = false;

    // Compile until all references are solved or three times is reached
    while ($compile && $count < 3) {

      // Always add one pass for when a large TOC is present, but do not check before the first run
      if ($output !== false && count(array_filter($output, array($this, 'findReferenceError'))) == 0) {
        $compile = false;
      }
      unset($output);

      $commandLine = sprintf(
          'cd %s && HOME="/tmp" %s %s -interaction=nonstopmode -output-directory="%s" "%s"',
          $this->outputDir,
          $this->pdfLatexBinaryLocation,
          $optionsString,
          $this->outputDir,
          $texLocation);
      $process     = $this->runProcess($commandLine);
      $output      = explode("\n", $process->getOutput());

      // Check if the pdflatex command completed successfully
      if (!$process->isSuccessful()) {
        throw new LatexParseException(
            $texLocation,
            $process->getExitCode(),
            $output,
            $process->getExitCodeText());
      }

      // Run bibliography tooling when enabled
      if ($count === 0 && !empty($this->bibliographyBinaryLocation)) {
        $bibLocation = explode('.tex', $texLocation)[0];
        $commandLine = sprintf(
            'cd %s && HOME="/tmp" %s "%s"',
            $this->outputDir,
            $this->bibliographyBinaryLocation,
            $bibLocation);
        $process     = $this->runProcess($commandLine);

        // Check whether the pdflatex command completed successfully
        if (!$process->isSuccessful()) {
          throw new BibliographyGenerationException(
              $texLocation,
              $process->getExitCode(),
              $process->getExitCodeText());
        }
      }

      $count++;
    }

    return $pdfLocation;
  }

  protected function getCacheBasePath(): string {
    return $this->cacheDir . '/BobvLatex/';
  }

  /**
   * Write the file to the cache directory
   *
   * @return string The location of the saved .tex file
   * @throws IOException
   */
  protected function writeTexFile(string $texData, string $fileName): string {
    $this->checkFilesystem();
    $this->outputDir = $this->getCacheBasePath() . hash('ripemd160', $texData) . '/';
    $texLocation     = $this->outputDir . $fileName . '.tex';
    try {

      // Do not regenerate unless cache is off (dev mode or force regenerate or passed maxAge)
      if ($this->filesystem->exists($texLocation)
          && $this->env == "prod"
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
      throw new IOException("An error occurred while creating the tex file at " . $ioe->getPath() . ". Is the cache writable?", 0, $ioe);
    }

    return $texLocation;
  }

  /**
   * Check if the line contains a reference error
   */
  private function findReferenceError(string $value): bool {
    return preg_match_all('/reference|change/ui', $value) > 0;
  }

  private function runProcess(string $commandLine): Process {
    $process = Process::fromShellCommandline($commandLine);

    $process->setTimeout($this->timeout);
    $process->run();

    return $process;
  }
}
