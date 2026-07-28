<?php

namespace Bobv\LatexBundle\Generator;

/** Contains information about the written TeX file. */
final readonly class TexFileInfo
{
  /**
   * @param string $directory The directory the TeX file has been written to
   * @param string $filename  The file name of the TeX file
   */
  public function __construct(
    public string $directory,
    public string $filename,
  ) {
  }

  public function getTexLocation(): string
  {
    return $this->directory . $this->filename;
  }
}
