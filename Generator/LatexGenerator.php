<?php

namespace BobV\LatexBundle\Generator;

use BobV\LatexBundle\Exception\ImageNotFoundException;
use BobV\LatexBundle\Exception\LatexException;
use BobV\LatexBundle\Latex\LatexBaseInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class LatexGenerator
 *
 * @todo: build some sort of cache
 */
class LatexGenerator
{

  /** @var string */
  protected $cacheDir;
  /** @var string */
  protected $env;
  /** @var Filesystem */
  protected $filesystem;
  /** @var LatexBaseInterface */
  protected $latex;
  /** @var string */
  protected $outputDir;
//  /** @var \DateTime */
//  protected $maxAge;
  /** @var \Twig_Environment */
  protected $twig;

  /**
   * @param                   $cacheDir
   * @param                   $env
   * @param \Twig_Environment $twig
   */
  public function __construct($cacheDir, $env, \Twig_Environment $twig)
  {
    $this->cacheDir   = $cacheDir;
    $this->env        = $env;
    $this->twig       = $twig;
    $this->filesystem = new Filesystem();
    $this->maxAge     = new \DateTime();
//    $this->maxAge->modify('-1 day');
  }

  /**
   * Compile a LaTeX object into the wanted PDF file
   *
   * @param \BobV\LatexBundle\Latex\LatexBaseInterface $latex
   *
   * @return string Location of the PDF document
   */
  public function generate(LatexBaseInterface $latex){
    $this->latex = $latex;
    $texLocation = $this->generateLatex();
    return $this->generatePdf($texLocation);
  }

  /**
   * Generates a latex file for the given LaTeX object
   *
   * @param \BobV\LatexBundle\Latex\LatexBaseInterface $latex
   *
   * @throws LatexException
   * @return string Location of the generated LaTeX file
   */
  protected function generateLatex(LatexBaseInterface $latex = null)
  {

    if($this->latex === null && $latex === null){
      throw new LatexException("No latex file given");
    }

    if($latex === null){
      $latex = $this->latex;
    }

    // Create the compiled tex-file
    // @todo: uitbreiden compilatie

    //var_dump($latex->getContext());die();
    $texData = $this->twig->render(
        $latex->getTemplate(),
        $latex->getContext()
    );

    // Check if there are undefined images
    $matches = array();
    preg_match_all('/\\includegraphics\[.+\]\{([^}]+)\}/u', $texData, $matches);
    foreach($matches[1] as $imageLocation){
      if(!$this->filesystem->exists($imageLocation)){
        throw new ImageNotFoundException($imageLocation);
      }
    }

    try {
      $texLocation = $this->writeTexFile($texData, $latex->getFileName());
    } catch (\Exception $e) {
      if ($e instanceof IOException || $e instanceof LatexException) {
        throw $e;
      }
      throw new LatexException("Something failed during the creation of the tex file. Check the logs for more info. (filename: " . $latex->getFileName() . ".log)");
    }

    return $texLocation;

  }

  /**
   * Generates a PDF from a given LaTeX object
   *
   * @param string $texLocation
   *
   * @return string Location of the generated PDF file
   * @throws \Symfony\Component\Filesystem\Exception\IOException
   * @throws LatexException
   */
  protected function generatePdf($texLocation)
  {

    // Check if the compiled tex file exists
    if (!$this->filesystem->exists($texLocation)){
      throw new IOException("The LaTeX file at $texLocation does not exist. Is the cache writable or did you forget to generate the .tex file?");
    }

    try {
      // Pdf's need to be compiled twice for correct references
      $this->compilePdf($texLocation);
      $pdfLocation = $this->compilePdf($texLocation);
    } catch (\Exception $e) {
      if ($e instanceof IOException || $e instanceOf LatexException) {
        throw $e;
      }
      throw new LatexException("Something failed during the compilation of the pdf file. Check the logs for more info. (filename: " . explode('.tex', $texLocation)[0] . ".log)");
    }

    return $pdfLocation;

  }

  /**
   * Returns the cache path
   */
  protected function getCacheBasePath()
  {
    return $this->cacheDir . '/BobVLatex/';
  }

  /**
   * Checks if the filesystem is ready to create the needed files
   *
   * @throws IOException
   */
  protected function checkFilesystem()
  {
    // Check if the cache dir exists
    if (!$this->filesystem->exists($this->cacheDir)) {
      try {
        $this->filesystem->mkdir($this->cacheDir);
      } catch (IOException $ioe) {
        throw new IOException("An error occurred while creating the cache directory at " . $ioe->getPath() . ". Is the cache writable?");
      }
    }
  }

  /**
   * Compile a PDF from a tex file on a given location
   * Compilation will be done twice to ensure correct references
   *
   * @param string $texLocation Location of the .tex file
   *
   * @throws \Exception
   * @return string
   */
  protected function compilePdf($texLocation)
  {
    $output = array();
    exec("pdflatex -interaction=nonstopmode -output-directory=\"" . $this->outputDir . "\" \"$texLocation\"", $output, $result);

    // Check if the result is ok
    if($result !== 0){
      throw new LatexException('Something went wrong during the execution of the pdflatex command. See the log file (' . explode('.tex', $texLocation)[0] .'.log) for more details.');
    }

    return explode('.tex', $texLocation)[0] . '.pdf';
  }

  /**
   * Write the file to the cache directory
   *
   * @param string $texData
   * @param        $fileName
   * @param bool   $regenerate
   *
   * @throws \Symfony\Component\Filesystem\Exception\IOException
   * @return string The location of the saved .tex file
   */
  protected function writeTexFile($texData, $fileName, $regenerate = NULL)
  {
    $this->checkFilesystem();
    $this->outputDir = $this->getCacheBasePath() . hash('ripemd160', $texData) . '/';
    $texLocation = $this->outputDir . $fileName . '.tex';
    try {

//      // Do not regenerate unless cache is off
//      if ($this->filesystem->exists($texLocation)) {
//        if ($regenerate === false || filemtime($texLocation) > $this->maxAge->getTimestamp()) {
//          return $texLocation;
//        }
//      }

      $this->filesystem->dumpFile(
          $texLocation,
          $texData
      );
    } catch (IOException $ioe) {
      throw new IOException("An error occurred while creating the tex file at " . $ioe->getPath() . ". Is the cache writable?");
    }

    return $texLocation;
  }
} 