<?php

namespace Helper;

use Bobv\LatexBundle\Helper\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{

  public function testSimpleNode(): void
  {
    $text = '<p>Some test text</p>';

    $this->assertEquals(
        "\nSome test text\n\n",
        Parser::parseHtml($text),
    );
  }

  public function testSimpleNodeWithBold(): void
  {
    $text = '<p>Some <b>test</b> text</p>';

    $this->assertEquals(
        "\nSome \\textbf{test} text\n\n",
        Parser::parseHtml($text),
    );
  }

  public function testSimpleNodeWithItalic(): void
  {
    $text = '<p>Some <em>test</em> text</p>';

    $this->assertEquals(
        "\nSome \\textit{test} text\n\n",
        Parser::parseHtml($text),
    );
  }

  public function testDoubleNode(): void
  {
    $text = '<p><b>Some</b> <em>test</em> text</p>';

    $this->assertEquals(
        "\n\\textbf{Some} \\textit{test} text\n\n",
        Parser::parseHtml($text),
    );
  }

  public function testDoubleOverlappingNode(): void
  {
    $text = '<p><b>Some <em>test</em></b> text</p>';

    $this->assertEquals(
        "\n\\textbf{Some \\textit{test}} text\n\n",
        Parser::parseHtml($text),
    );
  }

  public function testDoubleEqualNode(): void
  {
    $text = '<p>Some <b><em>test</em></b> text</p>';

    $this->assertEquals(
        "\nSome \\textbf{\\textit{test}} text\n\n",
        Parser::parseHtml($text),
    );
  }

  public function testEmptyText(): void {
    $parser = new Parser();

    $this->assertEquals('', $parser->parseText(null));
    $this->assertEquals('', $parser->parseText(''));
  }

  public function testEmptyHtml(): void {
    $this->assertEquals("\n\n", Parser::parseHtml(null));
    $this->assertEquals("\n\n", Parser::parseHtml(''));
  }
}
