<?php

namespace Bobv\LatexBundle\Exception;

/**
 * Simple \Exception extend for better error origin check
 */
class ImageNotFoundException extends \Exception
{
  public function __construct(private readonly string $imageLocation)
  {
    parent::__construct("The image used is not found. Did you provide the complete path? (provided path = $imageLocation)");
  }

  public function getImageLocation(): string
  {
    return $this->imageLocation;
  }
}
