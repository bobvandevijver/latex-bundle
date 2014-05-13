# Documentation
---------------------------------------

This page contains a full index of all pages contained in this documentation, based on the latest version.
If you're working with older versions we encourage you to update to latest build.
If you're getting errors or have trouble with update'ing see [submitting issues][1] and open a ticket on github.

[1]: https://github.com/bobv/latex-bundle/blob/master/Resources/doc/support/submitting-issues.md

* [Installation](https://github.com/bobvandevijver/latex-bundle/tree/master/Resources/doc/documentation.md#installation)
* [Usage](https://github.com/bobvandevijver/latex-bundle/tree/master/Resources/doc/documentation.md#usage)
* [Test the bundle](https://github.com/bobvandevijver/latex-bundle/tree/master/Resources/doc/documentation.md#test-the-bundle)

## Installation
--------------------

### 1. Add to composer

Add the LaTeX-bundle to your `composer.json`:

```json
"require": {
    "bobv/latex-bundle": "dev-master"
},
```

Or choose any other version from [packagist](https://packagist.org/packages/bobv/latex-bundle).

### 2. Enable the bundle

Enable the bundle in the `AppKernel.php` file
```php
<?php
public function registerBundles()
{
    $bundles = array(
        // ...
        new BobV\LatexBundle\BobVLatexBundle(),
    );
}
```

### 3. Install LaTeX on your machine

For the latex to pdf compilation you will need to have `pdflatex` installed. I can recommend to install these packages (these include most common used packages). 

```
texlive-base 
texlive-latex-base 
texlive-latex-extra
texlive-fonts-recommended
```

You can install them on Debian by issuing the following command:

```
sudo apt-get install texlive-base texlive-latex-base texlive-latex-extra texlive-fonts-recommended
```

That's it!

## Usage
-------------------------

The usage of this bundle is being kept as simple as possible. You just instantiate an LaTeX object and add blocks/elements to it. Three categories are made: 

* __*Base block*__

 Is the top block and this type can only be used once. Defines the document class used and needs a filename in the constructor. This is the object you will pass for generation and will contain all childs. Example: Article
* __*Section block*__
 
 A section block can also contain childs and defines most structure. Examples: Sections, box
* __*Element*__

 Can not contain childs. Examples: Text, TitlePage, TOC

Every block can have parameters which can be set by using the `setParam($param, $value)` method. See the block documentation for the supported params.
 
If you have any block to add to this bundle, please make a PR. Keep this categories in mind when developing a new block!

### 1. Creating a LaTeX object

To create a valid LaTeX object you should start by creating a new Base element, like `Article`. Every Base element needs to extend the `LatexBase` object. An example: 

```php
use BobV\LatexBundle\Latex\Base\Article;

$latex = new Article('BobV Latex Bundle');
```

For all possible Base classes check [here](https://github.com/bobvandevijver/latex-bundle/tree/master/Resources/doc/base).

### 2. Adding sections

Sections can easily be add to your LaTeX object by calling the addElement method. An example: 

```php
use BobV\LatexBundle\Latex\Section\TOC;

$latex->addElement(new TOC());
```

For all possible sections check [here](https://github.com/bobvandevijver/latex-bundle/tree/master/Resources/doc/section).

### 3. Adding elements

Elements can easily be added to your base LaTeX object. Just call the addElement method with any LaTeX (except Base) object. An example: 

```php
use BobV\LatexBundle\Latex\Element\TitlePage;

$latex->addElement(new TitlePage('Test title'));
```

For all possible elements check [here](https://github.com/bobvandevijver/latex-bundle/tree/master/Resources/doc/element).

### 4. Generate the latex/pdf files/response

This bundle can generate .tex files from the LaTeX objects, and can then generate pdf files from these .tex files. These files are generated in your cache directory, so make sure those are writable (doh). The files are placed at `{cache_dir}/BobVLatex/{hashed_content_string}/{filename}.{pdf/tex/log/aux/..}`. 

To create any of those files or send them in requests just use one of the following lines in one of your controllers: 

```php
use BobV\LatexBundle\Latex\Base\Article;
use BobV\LatexBundle\Latex\Element\TitlePage;
use BobV\LatexBundle\Latex\Element\TOC;

class DefaultController Extends Controller{
  public function sendPdf(Request $request){
    $latex = new Article();
    $latex->addElement(new Title('Test pdf file'));
    $latex->addElement(new TOC());
    
    $latexGenerator = $this->get('bobv.latex.generator');
    
    // Return a PDF Response from a LaTeX object
    return $latexGenerator->createPdfResponse($latex);
    
    // Return a TEX Response from a LaTeX object
    return $latexGenerator->createTexResponse($latex);
    
    // Compile a PDF from a LaTeX object
    $pdfLocation = $latexGenerator->generate($latex);
    
    // Compile a TEX from a LaTeX object
    $texLocation = $latexGenerator->generateLatex($latex);
    
    // Compile a PDF from a LaTeX file location
    $pdfLocation = $latexGenerator->generatePdf($texLocation);
  }
}
```

## Test the bundle
---------------------------

I've added a command which can be run to check if all settings for LaTeX are complete. It should return the pdf location if everything is ok. 

```
php app/console bobv:latex:test
```

If it detects problems, the errors are reported. 