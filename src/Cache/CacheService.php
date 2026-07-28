<?php

namespace Bobv\LatexBundle\Cache;

use Bobv\LatexBundle\Generator\GeneratorOptions;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

final readonly class CacheService
{
  public const CACHE_SUB_DIR = 'BobvLatex';

  private Filesystem $filesystem;

  public function __construct(private string $cacheDir)
  {
    $this->filesystem = new Filesystem();
  }

  public function getCacheBasePath(?GeneratorOptions $options): string
  {
    return ($options->cacheDir ?? $this->cacheDir) . DIRECTORY_SEPARATOR . self::CACHE_SUB_DIR . DIRECTORY_SEPARATOR;
  }

  public function ensureCacheDirExists(?GeneratorOptions $options): void
  {
    $cacheBasePath = $this->getCacheBasePath($options);

    // Check if the cache dir exists
    if ($this->filesystem->exists($cacheBasePath)) {
      return;
    }

    try {
      $this->filesystem->mkdir($cacheBasePath);
    } catch (IOException $ioe) {
      throw new IOException('An error occurred while creating the cache directory at ' . $ioe->getPath() . '. Is the cache writable?', 0, $ioe);
    }
  }
}
