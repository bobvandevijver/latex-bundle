<?php

namespace BobV\LatexBundle\Helper;

/**
 * Class Parser
 * @author BobV
 */
class Parser
{

  /**
   * @var array
   */
  private $htmlCodes;

  /**
   * Parser constructor.
   */
  public function __construct() {
    $this->htmlCodes = array_flip(get_html_translation_table(HTML_ENTITIES, ENT_QUOTES | ENT_HTML5));
  }

  /**
   * Parse the text and replace known special latex characters correctly
   *
   * @param string  $text
   * @param boolean $checkTable
   *
   * @return mixed
   */
  public function parseText($text, $checkTable = true) {

    // Try to replace HTML entities
    preg_match_all('/&[a-zA-Z]+;/iu', $text, $matches);
    foreach ($matches[0] as $match) {
      $text = str_replace($match, $this->htmlCodes[$match], $text);
    }
    $text = str_replace('&sup2;', '\\textsuperscript{2}', $text);
    $text = str_replace('&sup3;', '\\textsuperscript{3}', $text);

    $text = str_replace('²', '\\textsuperscript{2}', $text);
    $text = str_replace('³', '\\textsuperscript{3}', $text);

    // Remove remaining HTML entities
    $text = preg_replace('/&[a-zA-Z]+;/iu', '', $text);

    // Adjust known characters
    $text = str_replace("ä", "\\\"a", $text);
    $text = str_replace("á", "\\'a", $text);
    $text = str_replace("à", "\\`a", $text);
    $text = str_replace("Ä", "\\\"A", $text);
    $text = str_replace("Á", "\\'A", $text);
    $text = str_replace("À", "\\`A", $text);

    $text = str_replace("ë", "\\\"e", $text);
    $text = str_replace("é", "\\'e", $text);
    $text = str_replace("è", "\\`e", $text);
    $text = str_replace("ê", "\\^e", $text);
    $text = str_replace("Ë", "\\\"E", $text);
    $text = str_replace("É", "\\'E", $text);
    $text = str_replace("È", "\\`E", $text);
    $text = str_replace("Ê", "\\^E", $text);

    $text = str_replace("ï", "\\\"i", $text);
    $text = str_replace("í", "\\'i", $text);
    $text = str_replace("ì", "\\`i", $text);
    $text = str_replace("Ï", "\\\"I", $text);
    $text = str_replace("Í", "\\'I", $text);
    $text = str_replace("Ì", "\\`I", $text);

    $text = str_replace("ö", "\\\"o", $text);
    $text = str_replace("ó", "\\'o", $text);
    $text = str_replace("ò", "\\`o", $text);
    $text = str_replace("Ö", "\\\"O", $text);
    $text = str_replace("Ó", "\\'O", $text);
    $text = str_replace("Ò", "\\`O", $text);
    $text = str_replace("õ", "\\~O", $text);
    $text = str_replace("Õ", "\\~O", $text);

    $text = str_replace("ü", "\\\"u", $text);
    $text = str_replace("ú", "\\'u", $text);
    $text = str_replace("ù", "\\`u", $text);
    $text = str_replace("Ü", "\\\"U", $text);
    $text = str_replace("Ú", "\\'U", $text);
    $text = str_replace("Ù", "\\`U", $text);

    $text = str_replace("ñ", "\\~n", $text);
    $text = str_replace("ß", "{\\ss}", $text);
    $text = str_replace("ç", "\\c{c}", $text);
    $text = str_replace("Ç", "\\c{C}", $text);
    $text = str_replace("ș", "\\c{s}", $text);
    $text = str_replace("Ș", "\\c{S}", $text);
    $text = str_replace("ŭ", "\\u{u}", $text);
    $text = str_replace("Ŭ", "\\u{U}", $text);
    $text = str_replace("ă", "\\u{a}", $text);
    $text = str_replace("Ă", "\\u{A}", $text);
    $text = str_replace("ă", "\\v{a}", $text);
    $text = str_replace("Ă", "\\v{A}", $text);
    $text = str_replace("š", "\\v{s}", $text);
    $text = str_replace("Š", "\\v{S}", $text);
    $text = str_replace("Ø", "{\\O}", $text);
    $text = str_replace("ø", "{\\o}", $text);

    $text = str_replace("#", "\\#", $text);
    $text = str_replace("_", "\\_", $text);
    $text = str_replace("$", "\\$", $text);
    $text = str_replace("%", "\\%", $text);
    $text = str_replace("&", "\\&", $text);
    $text = str_replace("~", "\\~{}", $text);
    $text = str_replace("^", "\\^{}", $text);
    $text = str_replace("{", "\\{", $text);
    $text = str_replace("}", "\\}", $text);
    $text = str_replace(">", "\langle", $text);
    $text = str_replace("<", "\rangle", $text);
    $text = str_replace("\\", "\textbackslash", $text);

    // Check for & characters. Inside a tabular(x) env they should not be replaced
    $offset = 0;
    while ($checkTable && FALSE !== ($position = strpos($text, '&', $offset))) {
      if (!(strrpos($text, '\begin{tabular', $position - strlen($text)) < $position
          && strpos($text, '\end{tabular', $position) > $position)
      ) {
        $text     = substr_replace($text, '\\&', $position, 1);
        $position = $position + 3;
      }
      $offset = $position + 1;
      if ($offset > strlen($text)) {
        break;
      }
    }

    return $text;
  }

  /**
   * Parse the html input and create latex code of it
   *
   * @param $text
   *
   * @return mixed
   */
  public static function parseHtml($text) {

    // Replace UTF-8 nbsp; with normal space
    $text = str_replace("\xc2\xa0", ' ', $text);

    // Load the document, we force the usage of UTF-8
    $DOM = new \DOMDocument();
    @$DOM->loadHTML('<meta http-equiv="content-type" content="text/html; charset=utf-8">' . $text);

    // Replace tags with latex equivalents
    self::updateNode($DOM, 'b', '\textbf{');
    self::updateNode($DOM, 'strong', '\textbf{');
    self::updateNode($DOM, 'i', '\textit{');
    self::updateNode($DOM, 'u', '\uline{');
    self::updateNode($DOM, 'sup', '\textsuperscript{');
    self::updateNode($DOM, 'sub', '\textsubscript{');
    self::updateNode($DOM, 'ol', '\begin{enumerate}', '\end{enumerate}');
    self::updateNode($DOM, 'ul', '\begin{itemize}', '\end{itemize}');
    self::updateNode($DOM, 'li', '\item ', '');
    self::updateNode($DOM, 'p', '', "\n");
    self::updateNode($DOM, 'br', '\newline', '');
    self::updateNode($DOM, 'a', '\url{', ' }');

    // Export the new list
    $text = $DOM->saveHTML();

    // Replace junk from URL to get it readable
    $text = preg_replace("/(mailto:|https?:\/\/)?(([\da-z\.-]+)(\.|@)([a-z\.]{2,6})([\/\w\.-]*[\/\w-])*\/?)/smui", '$2', $text);

    return strip_tags($text);
  }

  /**
   * Encapsulate the content of all given tags in the given document with the replacement
   *
   * @param \DOMDocument $document
   * @param string       $tag
   * @param string       $replacementStart
   * @param string       $replacementEnd
   */
  private static function updateNode(\DOMDocument &$document, $tag, $replacementStart, $replacementEnd = '}') {
    $elements = $document->getElementsByTagName($tag);
    /** @var \DOMElement $element */
    foreach ($elements as $element) {

      // Check if a attribute from the tag is needed for the replacement start
      $replacementStartNode = $document->createTextNode($replacementStart);

      // Create the replacement end node
      $replacementEndNode = $document->createTextNode($replacementEnd);

      // Insert the nodes
      $element->parentNode->insertBefore($replacementStartNode, $element);
      $element->appendChild($replacementEndNode);
    }
  }
}
