<?php

namespace BobV\LatexBundle\Twig;

/**
 * Class BobVLatexExtension
 *
 * @author BobV
 */
class BobVLatexExtension extends \Twig_Extension{

  /**
   * @return array
   */
  public function getFilters(){
    return array(
      new \Twig_SimpleFilter('latex_escape', array($this, 'latexEscapeFilter')),
    );
  }

  /**
   * Parse the text and replace known special latex characters correctly
   *
   * @param $text
   */
  public function latexEscapeFilter($text){
    $text = str_replace("ä", "\\\"a", $text);
    $text = str_replace("á", "\\'a", $text);
    $text = str_replace("à", "\\`a", $text);
    $text = str_replace("Ä", "\\\"A", $text);
    $text = str_replace("Á", "\\'A", $text);
    $text = str_replace("À", "\\`A", $text);

    $text = str_replace("ë", "\\\"e", $text);
    $text = str_replace("é", "\\'e", $text);
    $text = str_replace("è", "\\`e", $text);
    $text = str_replace("Ë", "\\\"E", $text);
    $text = str_replace("É", "\\'E", $text);
    $text = str_replace("È", "\\`E", $text);

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

    return $text;
  }

  /**
   * @return string
   */
  public function getName(){
    return 'bobv_latex_twig_extension';
  }

} 