<?php

namespace Exception;

use Bobv\LatexBundle\Exception\LatexParseException;
use PHPUnit\Framework\TestCase;

class LatexParseExceptionTest extends TestCase
{
  /** Test case to test the exception handler on a log/tex file of your choice. */
  public function testErrorParsing(): void
  {
    $exception = new LatexParseException(
        __DIR__ . DIRECTORY_SEPARATOR . 'test.tex',
        1,
        explode("\n", file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'test.log')),
        'General error',
    );

    $this->assertNotEmpty($exception->getFilteredLogSource());
    $this->assertStringContainsString('l.9 ...somefile.jpg}', $exception->getFilteredLogSource());
    $this->assertNotEmpty($exception->getFilteredTexSource());
    $this->assertStringContainsString('\includegraphics[width=0.95\textwidth,totalheight=0.95\textheight,keepaspectratio]{/var/www/somefile.jpg}', $exception->getFilteredTexSource());
  }
}
