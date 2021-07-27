<?php

namespace Twig;

use BobV\LatexBundle\Twig\BobVLatexExtension;
use PHPUnit\Framework\TestCase;

class BobVLatexExtensionTest extends TestCase
{
  /**
   * @dataProvider transliterateProvider
   */
  public function testTransliteration(bool $useSymfonyString, string $input, string $expectedOutput) {
    $parser = new BobVLatexExtension($useSymfonyString);

    $this->assertEquals($expectedOutput, $parser->latexEscape($input));
  }

  public function transliterateProvider(): iterable {
    yield [true, 'ậ', 'a'];
    yield [false, 'ậ', 'ậ'];
    yield [true, 'â', '\^a'];
    yield [false, 'â', '\^a'];
    yield [true, 'Đ', 'D'];
    yield [false, 'Đ', 'Đ'];
    yield [true, 'ễ', 'e'];
    yield [false, 'ễ', 'ễ'];
    yield [true, 'ê', '\^e'];
    yield [false, 'ê', '\^e'];
    yield [true, 'ậâĐễê', 'a\^aDe\^e'];
    yield [false, 'ậâĐễê', 'ậ\^aĐễ\^e'];
  }
}
