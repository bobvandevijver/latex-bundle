<?php

namespace BobV\LatexBundle\Exception;

/**
 * Class ImageNotFoundException
 * Simple \Exception extend for better error origin check
 */
class ImageNotFoundException extends \Exception
{
  private $imageLocation;

  public function __construct($imageLocation)
  {
    $this->imageLocation = $imageLocation;

    parent::__construct("The image used is not found. Did you provide the complete path? (provided path = $imageLocation)");
  }

  /**
   * @return mixed
   */
  public function getImageLocation()
  {
    return $this->imageLocation;
  }
}
