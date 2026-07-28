<?php

namespace Bobv\LatexBundle\Generator;

/** Contains information about the written PDF file. */
final readonly class PdfFileInfo
{
  /**
   * @param string $directory The directory the PDF file has been written to
   * @param string $filename  The file name of the PDF file
   */
  public function __construct(
    public string $directory,
    public string $filename,
  ) {
  }

  public function getPdfLocation(): string
  {
    return $this->directory . $this->filename;
  }
}
