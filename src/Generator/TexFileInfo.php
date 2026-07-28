<?php

namespace Bobv\LatexBundle\Generator;

use InvalidArgumentException;

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
    if (!str_ends_with($this->directory, DIRECTORY_SEPARATOR)) {
      throw new InvalidArgumentException('Directory must contain a trailing directory separator');
    }

    if (!str_ends_with($this->filename, '.tex')) {
      throw new InvalidArgumentException('Filename must end with .tex');
    }
  }

  public function getTexLocation(): string
  {
    return $this->directory . $this->filename;
  }
}
