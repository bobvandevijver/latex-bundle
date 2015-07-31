<?php

namespace BobV\LatexBundle\Latex\Element;

use BobV\LatexBundle\Latex\LatexElement;

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
    $this->template = 'BobVLatexBundle:Element:includepdf.tex.twig';
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
    exec("pdfinfo $document", $output);

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