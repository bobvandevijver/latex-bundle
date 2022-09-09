<?php

namespace Bobv\LatexBundle\Latex\Element;

use Bobv\LatexBundle\Latex\LatexElement;
use Symfony\Component\Process\Process;

/**
 * Class IncludePdf
 * Include PDF
 *
 * @author BobV
 */
class IncludePdf extends LatexElement
{

  /**
   * Constructor of the include pdf element, also defines the defaults
   *
   * @param string $fileLocation
   * @param boolean $skipFirstWallpaper
   */
  public function __construct($fileLocation, $skipFirstWallpaper = true)
  {
    // Check amount of pages to avoid a newpage
    if (!$skipFirstWallpaper){
      $totalPages = $this->getPDFPages($fileLocation);
      $pages = '{2-}';
    }else{
      $totalPages = 0;
      $pages = '{1-}';
    }

    // Define defaults
    $this->template = '@BobvLatex/Element/includepdf.tex.twig';
    $this->params   = array(
        'totalPages'           => $totalPages,
        'skip_first_wallpaper' => $skipFirstWallpaper,
        'pages'                => $pages,
        'width'                => '\textwidth',
        'frame'                => 'false',
        'file_location'        => $fileLocation,
        'more_options'         => '',
        'y_offset'             => '0pt',
        'x_offset'             => '0pt',
        'wallpaper_scaling'    => '0.83'
    );
  }

  /**
   * Source: http://stackoverflow.com/questions/14644353/get-the-number-of-pages-in-a-pdf-document
   *
   * @param $document
   *
   * @return int
   */
  private function getPDFPages($document)
  {
    $commandLine     = sprintf('pdfinfo %s', $document);
    if (method_exists(Process::class, 'fromShellCommandline')) {
      $process = Process::fromShellCommandline($commandLine);
    } else {
      $process = new Process($commandLine);
    }

    $process->run();
    if($process->getExitCode() !== 0){
      return 0;
    }
    $output = explode("\n", $process->getOutput());

    // Iterate through lines
    $pagecount = 0;
    foreach ($output as $op) {
      // Extract the number
      if (preg_match("/Pages:\s*(\d+)/i", $op, $matches) === 1) {
        $pagecount = intval($matches[1]);
        break;
      }
    }

    return $pagecount;
  }

}
