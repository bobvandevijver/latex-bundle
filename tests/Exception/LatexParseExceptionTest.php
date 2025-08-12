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

  public function testExactMessage(): void {
    $texLocation    = __DIR__ . DIRECTORY_SEPARATOR . 'test';
    $exception      = new LatexParseException(
        $texLocation . '.tex',
        1,
        explode("\n", file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'test.log')),
        'General error',
    );

    $this->assertEquals("Something went wrong during the execution of the pdflatex command, as it returned 1 (General error). See the log file ($texLocation.log) for all details.


Below some more info is tried to extract from the error:
---
%&-line parsing enabled.
LaTeX Warning: Reference `LastPage' on page 21 undefined on input line 4110.
[21 </var/www/somefile.jpg>]
! Dimension too large.
<argument> \ht \@tempboxa
l.9 ...somefile.jpg}
I can't work with sizes bigger than about 19 feet.
Continue and I'll use the largest value I can.
---", $exception->getExtendedMessage());
    $this->assertEquals("---
%&-line parsing enabled.
LaTeX Warning: Reference `LastPage' on page 21 undefined on input line 4110.
[21 </var/www/somefile.jpg>]
! Dimension too large.
<argument> \ht \@tempboxa
l.9 ...somefile.jpg}
I can't work with sizes bigger than about 19 feet.
Continue and I'll use the largest value I can.
---", $exception->getFilteredLogSource());
    $this->assertEquals("---
l.9
\\documentclass{book}
\\begin{document}
{\\normalfont\huge\bfseries}{}{\colorbox{black}{\parbox{\dimexpr\\textwidth-2\\fboxsep\\relax}{\\textcolor{white}{\\textbf{File}}}}}
\\vspace{-0.5cm}
% Graphic element
\begin{figure}[h!]
\includegraphics[width=0.95\\textwidth,totalheight=0.95\\textheight,keepaspectratio]{/var/www/somefile.jpg}
\\centering
\\label{fig:somefile.jpg}
\\end{figure}
% Custom command
\\newpage
\\end{document}
---", $exception->getFilteredTexSource());
  }
}
