<?php

namespace Bobv\LatexBundle\Generator;

use InvalidArgumentException;

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
    if (!str_ends_with($this->directory, DIRECTORY_SEPARATOR)) {
      throw new InvalidArgumentException('Directory must contain a trailing directory separator');
    }

    if (!str_ends_with($this->filename, '.pdf')) {
      throw new InvalidArgumentException('Filename must end with .pdf');
    }
  }

  public function getPdfLocation(): string
  {
    return $this->directory . $this->filename;
  }
}
