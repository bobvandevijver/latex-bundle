<?php

namespace BobV\LatexBundle\Generator;

use BobV\LatexBundle\Exception\ImageNotFoundException;
use BobV\LatexBundle\Exception\LatexException;
use BobV\LatexBundle\Latex\LatexBaseInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
  /** @var bool */
  protected $forceRegenerate;
  /** @var LatexBaseInterface */
  protected $latex;
  /** @var \DateTime */
  protected $maxAge;
  /** @var string */
  protected $outputDir;
  /** @var \Twig_Environment */
  protected $twig;

  /**
   * @param                   $cacheDir
   * @param                   $env
   * @param \Twig_Environment $twig
   */
  public function __construct($cacheDir, $env, \Twig_Environment $twig, $maxAge)
  {
    $this->cacheDir   = $cacheDir;
    $this->env        = $env;
    $this->twig       = $twig;
    $this->filesystem = new Filesystem();
    $this->maxAge     = new \DateTime();
    $this->maxAge->modify($maxAge);
    $this->forceRegenerate = false;
  }

  /**
   * Generate a response containing a PDF document
   *
   * @param LatexBaseInterface $latex
   *
   * @return StreamedResponse
   */
  public function createPdfResponse(LatexBaseInterface $latex)
  {
    $pdfLocation = $this->generate($latex);

    $response = new BinaryFileResponse($pdfLocation);
    $response->headers->set('Content-Type', 'application/pdf;charset=utf-8');
    $response->headers->set('Content-Disposition', 'attachment;filename="' . $latex->getFileName() . '.pdf"');

    return $response;
  }

  /**
   * Generate a response containing a generated .tex file
   *
   * @param LatexBaseInterface $latex
   *
   * @return BinaryFileResponse
   */
  public function createTexResponse(LatexBaseInterface $latex)
  {
    $texLocation = $this->generateLatex($latex);

    $response = new BinaryFileResponse($texLocation);
    $response->headers->set('Content-Type', 'application/x-tex;charset=utf-8');
    $response->headers->set('Content-Disposition', 'attachment;filename="' . $latex->getFileName() . '.tex"');

    return $response;
  }

  /**
   * Compile a LaTeX object into the wanted PDF file
   *
   * @param \BobV\LatexBundle\Latex\LatexBaseInterface $latex
   * @param string $outputDir
   *
   * @return string Location of the PDF document
   */
  public function generate(LatexBaseInterface $latex, $outputDir = null)
  {
    $this->latex = $latex;
    $texLocation = $this->generateLatex();

	if(!$outputDir) {
		$outputDir = $this->getCacheBasePath();
	}
	
    return $this->generatePdf($texLocation, $outputDir);
  }

  /**
   * Generates a latex file for the given LaTeX object
   *
   * @param \BobV\LatexBundle\Latex\LatexBaseInterface $latex
   *
   * @throws LatexException
   * @return string Location of the generated LaTeX file
   */
  public function generateLatex(LatexBaseInterface $latex = NULL)
  {

    if ($this->latex === NULL && $latex === NULL) {
      throw new LatexException("No latex file given");
    }

    if ($latex === NULL) {
      $latex = $this->latex;
    }

    // Create the compiled tex-file
    // @todo: uitbreiden compilatie

    $texData = $this->twig->render(
        $latex->getTemplate(),
        $latex->getContext()
    );

    // Check if there are undefined images
    $matches = array();
    preg_match_all('/\\includegraphics\[.+\]\{([^}]+)\}/u', $texData, $matches);
    foreach ($matches[1] as $imageLocation) {
      if (!$this->filesystem->exists($imageLocation)) {
        throw new ImageNotFoundException($imageLocation);
      }
    }

    try {
      $texLocation = $this->writeTexFile($texData, $latex->getFileName());
    } catch (\Exception $e) {
      if ($e instanceof IOException || $e instanceof LatexException) {
        throw $e;
      }
      throw new LatexException("Something failed during the creation of the tex file. Check the logs for more info. (filename: " . $latex->getFileName() . ".log )");
    }

    // Copy dependencies to working dir
    foreach ($latex->getDependencies() as $dependency) {
      if ($this->filesystem->exists($dependency)) {
        $finder = new Finder();
        $finder->files()->in($dependency)->depth('== 0');;
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
   * @param string $texLocation
   * @param string $outputDir
   * @param array $options
   *
   * @return string Location of the generated PDF file
   * @throws \Symfony\Component\Filesystem\Exception\IOException
   * @throws LatexException
   */
  public function generatePdf($texLocation, $outputDir, array $options = null)
  {

    // Check if the compiled tex file exists
    if (!$this->filesystem->exists($texLocation)) {
      throw new IOException("The LaTeX file at $texLocation does not exist. Is the cache writable or did you forget to generate the .tex file?");
    }

    try {
      $pdfLocation = $this->compilePdf($texLocation, $outputDir, $options);
    } catch (\Exception $e) {
      if ($e instanceof IOException || $e instanceOf LatexException) {
        throw $e;
      }
      throw new LatexException("Something failed during the compilation of the pdf file. Check the logs for more info. (filename: " . explode('.tex', $texLocation)[0] . ".log )");
    }

    return $pdfLocation;

  }

  /**
   * @param boolean $forceRegenerate
   */
  public function setForceRegenerate($forceRegenerate)
  {
    $this->forceRegenerate = $forceRegenerate;
  }

  /**
   * @param \DateTime $maxAge
   */
  public function setMaxAge($maxAge)
  {
    $this->maxAge = $maxAge;
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
   * If necessary, compilation will be done up to three times for correct references
   *
   * @param string $texLocation Location of the .tex file
   * @param string $outputDir
   * @param array $options
   *
   * @throws \Exception
   * @return string
   */
  protected function compilePdf($texLocation, $outputDir, array $options = null)
  {

    // check if output directory exists
	if(!$this->filesystem->exists($outputDir)) {
		try {
			$this->filesystem->mkdir($outputDir);
		} catch(IOException $ioe) {
			throw new IOException(sprintf('Failed creating the output directory "%s".',$outputDir));
		}
	}
	
	// check if there are additional options
	$optionsString = '';
	if(is_array($options)) {
		foreach($options as $option => $value) {
			$optionsString .= ' -' . $option . (($value) ? ' ' . $value . ' ' : ' ');
		}
	}
	
    $pdfLocation = explode('.tex', $texLocation)[0] . '.pdf';

    // Do not regenerate unless cache is off (dev mode or force regenerate or passed maxAge)
    if ($this->filesystem->exists($pdfLocation)
        && $this->env == "prod"
        && filemtime($pdfLocation) > $this->maxAge->getTimestamp()
        && !$this->forceRegenerate
    ) {
      return $pdfLocation;
    }

    $compile = true;
    $count   = 0;

    // Compile until all references are solved or three times is reached
    while ($compile && $count < 3) {

      exec("cd " . $outputDir . " && pdflatex " . $optionsString . "-interaction=nonstopmode -output-directory=\"" . $outputDir . "\" \"$texLocation\"", $output, $result);

      // Check if the result is ok
      if ($result !== 0) {
        throw new LatexException('Something went wrong during the execution of the pdflatex command, as it returned ' . $result . '. See the log file ( ' . explode('.tex', $texLocation)[0] . '.log ) for more details.');
      }

      if (count(array_filter($output, array($this, 'findReferenceError'))) == 0) {
        $compile = false;
      }

      unset($output);
      $count++;
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
   * Write the file to the cache directory
   *
   * @param string $texData
   * @param        $fileName
   *
   * @throws \Symfony\Component\Filesystem\Exception\IOException
   * @return string The location of the saved .tex file
   */
  protected function writeTexFile($texData, $fileName)
  {
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
      throw new IOException("An error occurred while creating the tex file at " . $ioe->getPath() . ". Is the cache writable?");
    }

    return $texLocation;
  }

  /**
   * Check if the line contains a reference error
   *
   * @param $value
   *
   * @return bool
   */
  private function findReferenceError($value)
  {
    return count(preg_match_all('/reference|change/ui', $value)) > 0;
  }


} 