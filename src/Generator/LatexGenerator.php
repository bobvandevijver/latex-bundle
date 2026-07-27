<?php

namespace Bobv\LatexBundle\Generator;

use Bobv\LatexBundle\Compiler\CompilerContext;
use Bobv\LatexBundle\Compiler\LatexCompilerInterface;
use Bobv\LatexBundle\Exception\BibliographyGenerationException;
use Bobv\LatexBundle\Exception\ImageNotFoundException;
use Bobv\LatexBundle\Exception\LatexException;
use Bobv\LatexBundle\Exception\LatexParseException;
use Bobv\LatexBundle\Latex\LatexBaseInterface;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Twig\Environment;

class LatexGenerator implements LatexGeneratorInterface
{
  protected Filesystem $filesystem;
  protected ?string $outputDir         = null;

  public function __construct(
    protected readonly string $cacheDir,
    protected readonly string $env,
    protected readonly Environment $twig,
    protected LatexCompilerInterface $latexCompiler,
    protected string $maxAgeModifier,
  ) {
    $this->filesystem = new Filesystem();
  }

  public function createPdfResponse(LatexBaseInterface $latex, bool $download = true, ?GeneratorOptions $options = null): BinaryFileResponse
  {
    $pdfLocation = $this->generate($latex, $options);

    $response = new BinaryFileResponse($pdfLocation);
    $response->headers->set('Content-Type', 'application/pdf;charset=utf-8');
    $response->headers->set('Content-Disposition',
      sprintf('%sfilename="%s.pdf"', $download ? 'attachment;' : '', $latex->getFileName()));

    return $response;
  }

  public function createTexResponse(LatexBaseInterface $latex, bool $download = true, ?GeneratorOptions $options = null): BinaryFileResponse
  {
    $texLocation = $this->generateLatex($latex, $options);

    $response = new BinaryFileResponse($texLocation);
    $response->headers->set('Content-Type', 'application/x-tex;charset=utf-8');
    $response->headers->set('Content-Disposition',
      sprintf('%sfilename="%s.tex"', $download ? 'attachment;' : '', $latex->getFileName()));

    return $response;
  }

  public function generate(LatexBaseInterface $latex, ?GeneratorOptions $options = null): string
  {
    $texLocation = $this->generateLatex($latex, $options);

    return $this->generatePdf($texLocation, $options);
  }

  public function generateLatex(LatexBaseInterface $latex, ?GeneratorOptions $options = null): string
  {
    $options ??= new GeneratorOptions();

    // Create the compiled tex-file
    $texData = $this->twig->render(
      $latex->getTemplate(),
      $latex->getContext(),
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
      $texLocation = $this->writeTexFile($texData, $latex->getFileName(), $options);
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
          /* @var \SplFileInfo $file */
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

  public function generatePdf(string $texLocation, ?GeneratorOptions $options = null): string
  {
    $options ??= new GeneratorOptions();

    // Check if the compiled tex file exists
    if (!$this->filesystem->exists($texLocation)) {
      throw new IOException("The LaTeX file at $texLocation does not exist. Is the cache writable or did you forget to generate the .tex file?");
    }

    try {
      $pdfLocation = $this->compilePdf($texLocation, $options);
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
  protected function checkFilesystem(GeneratorOptions $options): void
  {
    $cacheBasePath = $this->getCacheBasePath($options);

    // Check if the cache dir exists
    if (!$this->filesystem->exists($cacheBasePath)) {
      try {
        $this->filesystem->mkdir($cacheBasePath);
      } catch (IOException $ioe) {
        throw new IOException('An error occurred while creating the cache directory at ' . $ioe->getPath() . '. Is the cache writable?', 0, $ioe);
      }
    }
  }

  /**
   * Compile a PDF from a tex file on a given location
   * If necessary, compilation will be done up to three times for correct references.
   *
   * @param string           $texLocation Location of the .tex file
   * @param GeneratorOptions $options     Generator options
   *
   * @return string
   * @throws BibliographyGenerationException
   * @throws LatexException
   * @throws LatexParseException
   */
  protected function compilePdf(string $texLocation, GeneratorOptions $options): string
  {
    $baseName    = basename($texLocation, '.tex');
    $pdfLocation = explode('.tex', $texLocation)[0] . '.pdf';

    // Do not regenerate unless cache is off (dev mode or force regenerate or passed maxAge)
    if ($this->filesystem->exists($pdfLocation)
        && $this->env == 'prod'
        && filemtime($pdfLocation) > $this->getMaxAgeTimestamp($options)
        && !$options->forceRegenerate
    ) {
      return $pdfLocation;
    }

    // Get the output directory from the tex file
    $this->outputDir = dirname($texLocation);

    // Process extra options for security
    $pdflatexArgs = [];
    foreach ($options->compileOptions as $option => $value) {
      $pdflatexArgs[] = sprintf('-%s %s', $option, $value ?: '');
    }

    // Add -no-shell-escape
    if (!$options->shellEscape) {
      $pdflatexArgs[] = '-no-shell-escape';
    }

    // Add -openin_any=p and -openout_any=p
    if ($options->openInAny !== null) {
      $pdflatexArgs[] = "-cnf-line=openin_any=$options->openInAny";
    }
    if ($options->openOutAny !== null) {
      $pdflatexArgs[] = "-cnf-line=openout_any=$options->openOutAny";
    }

    // Always add interaction mode
    $pdflatexArgs[] = '-interaction=nonstopmode';

    $compile = true;
    $count   = 0;
    /** @var false|string[] $output */
    $output = false;

    // Create context for the current run
    $context = new CompilerContext($this->outputDir, $baseName, $pdflatexArgs);

    // Compile until all references are solved or three times is reached
    while ($compile && $count < 3) {
      // Always add one pass for when a large TOC is present, but do not check before the first run
      if ($output !== false && count(array_filter($output, $this->findReferenceError(...))) == 0) {
        $compile = false;
      }
      unset($output);

      $result = $this->latexCompiler->pdflatex($context);
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
        $result = $this->latexCompiler->bibtex($context);

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

  protected function getCacheBasePath(GeneratorOptions $options): string
  {
    return ($options->cacheDir ?? $this->cacheDir) . '/BobvLatex/';
  }

  /**
   * Write the file to the cache directory.
   *
   * @throws IOException
   *
   * @return string The location of the saved .tex file
   */
  protected function writeTexFile(string $texData, string $fileName, GeneratorOptions $options): string
  {
    $this->checkFilesystem($options);
    $this->outputDir = $this->getCacheBasePath($options) . hash('ripemd160', $texData) . '/';
    $texLocation     = $this->outputDir . $fileName . '.tex';
    try {
      // Do not regenerate unless cache is off (dev mode or force regenerate or passed maxAge)
      if ($this->filesystem->exists($texLocation)
          && $this->env == 'prod'
          && filemtime($texLocation) > $this->getMaxAgeTimestamp($options)
          && !$options->forceRegenerate
      ) {
        return $texLocation;
      }

      $this->filesystem->dumpFile($texLocation, $texData);
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

  private function getMaxAgeTimestamp(GeneratorOptions $options): int
  {
    if ($options->maxAge) {
      return $options->maxAge->getTimestamp();
    }

    return (new DateTimeImmutable())->modify($this->maxAgeModifier)->getTimestamp();
  }
}
