# Cookbook

Below examples are given of how you can extend the bundle. Note that not all of them are recommended.

## Direct Twig template rendering

> **Note**: I do not recommend this usage as you bypass the object oriented approach this bundle uses.

You can use the `DirectTwig` class below:
```php
use BobV\LatexBundle\Latex\LatexBase;

class DirectTwig extends LatexBase
{
  public function __construct($template) {
    // The template name
    $this->template = $template;

    // Call the parent constructor for a filename
    parent::__construct(str_replace('/', '_', $template));
  }
  
  public function setContext($context){
    // Twig parameters
    $this->params   = $context;
    
    return $this;
  }
}
```

Create the PDF from you twig template:
```php
return $latexGenerator->createPdfResponse(
  (new DirectTwig('article/recent_list.tex.twig'))
    ->setContext(array('articles' => $articles))
));
```

This class is based on the question and answer in [#30](https://github.com/bobvandevijver/latex-bundle/issues/30).
