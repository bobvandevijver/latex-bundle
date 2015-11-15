<?php
namespace BobV\LatexBundle\Helper;

/**
 * Class Sanitizer
 * @author BobV
 */
class Sanitize
{

  /**
   * Function to sanitize given text for safe usage in a file name
   *
   * @param string $text
   *
   * @return string
   */
  public static function sanitizeText($text) {
    $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
        "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
        "â€”", "â€“", ",", "<", ".", ">", "/", "?");

    $text = trim(str_replace($strip, "", strip_tags($text)));

    return preg_replace('!\s+!', ' ', $text);
  }
}