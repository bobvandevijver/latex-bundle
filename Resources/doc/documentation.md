# Documentation

This page contains a full index of all pages contained in this documentation, based on the latest version.
If you're working with older versions we encourage you to update to latest build.
If you're getting errors or have trouble with update'ing see [submitting issues][1] and open a ticket on github.

[1]: https://github.com/bobv/latex-bundle/blob/master/Resources/doc/support/submitting-issues.md

* [Installation](#installation)
* [Usage](#usage)
* [Exceptions](#exceptions)
* [Character escaping](#character-escaping)
* [HTML to LaTeX](#html-to-latex)
* [Custom output directory](#custom-output-directory)
* [pdflatex location](#pdflatex-location)
* [Adding extra fonts](#adding-extra-fonts)
* [Test the bundle](#test-the-bundle)

## Installation

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

For the use of the includepdf command, you might need the pdfinfo command, which is located in the following package:
```
poppler-utils
```

You can install them on Debian by issuing the following command:

```
sudo apt-get install texlive-base texlive-latex-base texlive-latex-extra texlive-fonts-recommended poppler-utils
```

The default latex installation does not contain the `ulem` package, which is needed for correct wrapping of underlined lines (see [tex.stackexchange.com](http://tex.stackexchange.com/questions/9550/why-does-underlined-text-not-get-wrapped-once-it-hits-the-end-of-a-line). To install the package, run the following (again, assuming Debian):

```
sudo mkdir /usr/share/texmf/tex/latex/ulem
sudo wget https://raw.githubusercontent.com/bobvandevijver/latex-bundle/master/Resources/packages/ulem.sty -P /usr/share/texmf/tex/latex/ulem
sudo texhash
```

That's it!

## Usage

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
    // Subtitle, author, date are optional
    $latex->addElement(new TitlePage('BobV Latex Test', 'a subtitle', 'an author', '\today'));
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

Note that the `generatePdf` also accepts an optional `$compileOptions` array. With this array you can specify extra compile options for the `pdflatex` command.

> Note: If you have extremely large files, you might hit the default Process limit of 60 seconds during the execution of pdflatex (from the Symfony Process component). If so, you can customize the timeout with the `setTimout($timeout)` method on the `LatexGenerator`.

If you're using a Symfony version which has autowiring enabled, you can also autowire the generator by using the `LatexGeneratorInterface` interface!

```php
use BobV\LatexBundle\Latex\Base\Article;
use BobV\LatexBundle\Generator\LatexGeneratorInterface;

public function renderLatex(LatexGeneratorInterface $generator){
  $latex = new Article();
  return $generator->createPdfResponse($latex);
}
```

### 5. Caching

By default, this latexbundle will not dump the texfile or regenerate the PDF file if the file already exists. This is done by using a hash of the tex contents to ensure that when a file is changed, the generation will be done always.

However, when a file still has the same hash, and the environment is prod, caching will be used. By default, a max file age of `1 day` is used. This is customizable using the `bobv.latex.maxage` parameter in your `parameters.yml`.

It is also possible to temporarely force a regeneration: just use the right method on the generator:

```
$latexGenerator = $this->get('bobv.latex.generator');
$latexGenerator->setForceRegenerate(true);

// Or when the max age must be altered
$dateTime = new \DateTime();
$latexGenerator->setMaxAge($dateTime);
```

## Exceptions

This bundle is shipping with a few exception classes. These are al follows:

* ImageNotFoundException: Thrown when a image in the generated tex file is not located on the disk.
* LatexException: Base LaTeX exception class, thrown with explaining message
* LatexNotImplementedException: Currently not used, but meant to signal not implemented behaviour
* LatexParseException: Thrown when the compilation of the tex file to pdf fails. 

The last exception also includes a backtrace in it message which can be used to find the exact point of failure in the tex file. It includes a filtered log and tex file which can be used when required. 

## Character escaping

This bundle includes a simple text parser which can escape most UTF-8 characters like ö to \"o. The method `parseText` in the `Helper/Parser` class takes the text to parse as argument and return the parsed text.

> **Note**: By default, the filter is not meant to remove latex commands! It is meant to convert characters that might be used by your users into a character that LaTeX understands. However, when the parameters are set correctly, you can achieve the remove all behavior.

This method is also available in Twig as a simple filter: `latex_escape`. This filter is applied automatically when using the standard objects/templates. Note that the filter takes arguments which can change the behavior of the text parser. For the most up-to-date arguments check the [`Parser` class](https://github.com/bobvandevijver/latex-bundle/blob/master/Helper/Parser.php#L34). 

Next to the `latex_escape` filter, there is also a `latex_escape_all` filter. This should remove all LaTeX commands. 

The parser takes three arguments: `checkTable`, `removeLatex` and `parseNewLines`. The first is by default `true`, while the other two are `false` by default. If you do not want to use the table checking (the check for the & char is disabled, which might by handy in table environments), set the first argument to `false`. To remove all Latex commands from the input, set the second parameter to `true`. This disables the possibility to use custom commands from your input. The third argument can be set to `true` in order to replace all newline characters into LaTeX commands for newlines.

If you have any character that generates an error, feel create [an issue](https://github.com/bobvandevijver/latex-bundle/issues/new) or create a PR.

## PDF to image

If you want this bundle to render for example LaTeX-equations as image, you can use the following code (this does require you to have [spatie/pdf-to-image](https://github.com/spatie/pdf-to-image) installed). For example:

```php
use BobV\LatexBundle\Generator\LatexGeneratorInterface;
use BobV\LatexBundle\Latex\Base\Standalone;
use BobV\LatexBundle\Latex\Element\CustomCommand;
use Spatie\PdfToImage\Pdf;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

public function renderLatex(LatexGeneratorInterface $generator)
{
  // Create latex object
  $content = '$ x^2 = y^2 = x^2 $';
  $document = (new Standalone(md5($content)))
      ->addElement(new CustomCommand($content));

  // Generate pdf output
  $pdfLocation = $generator->generate($document);

  // Determine output location
  $imageLocation = str_replace('.pdf', '.jpg', $pdfLocation);

  // Convert to image
  $pdf = new Pdf($pdfLocation);
  $pdf->setOutputFormat('jpg');
  $pdf->saveImage($imageLocation);

  // Return image
  return new BinaryFileResponse($imageLocation);
}
```

## HTML to LaTeX

This bundle also includes a HTML to LaTeX parser, which will parse basic HTML structures and convert it to basic LaTeX syntax. At the moment the following tags are supported: 

`b, strong, em, u, sup, sub (requires fixltx2e package), ol, ul, li, a, p and br`

NOTE: The parser assumes that the HTML input is generated using a CK-editor instance. When this is not the case, the behaviour might be unexpected!

It is recommended to use the `bobv_latex` form type when you are in need for a stylable input to LaTeX. This form field type will be updated with new functions when available. Note that to use this form field, you will need to require the suggested `egeloen/ckeditor-bundle` in your `composer.json`.  

If you have any use-case that generates an error, feel create an issue or create a PR.

## Custom output directory

By default, this bundle uses the `%bobv.latex.cache_dir%` parameter for the caching directory, which is by default set to `"%kernel.cache_dir%"`. If you want to override this directory, simply override the parameter. However, do note that the kernel cache dir is used for a reason: to generate a correct PDF file, all dependencies (if any, like fonts, letterhead, ...) are also placed there. By placing it in the cache, we know it can easily and automatically will be removed so that the disk does not constantly fill up.

It is also possible to override the cache directory per request, by using the `setCacheDir()` method on the `LatexGenerator`.

By default, the cache directory is appended with `/BobVLatex`. This can be adjusted by extending the `LatexGenerator` class and overwriting the `getCacheBasePath()` method. 

## pdflatex location

This bundles uses the `%bobv.latex.pdflatex.location%` parameter as pdflatex executable location, which is set to `pdflatex` by default. This means that the executable should be available in your path, which would be the case in the most default installation. If your executable is not available from the path, make sure to put the absolute executable path in your configuration.

## Adding extra fonts

See [here](https://github.com/bobvandevijver/latex-bundle/tree/master/Resources/doc/font/font.md).

## Test the bundle

I've added a command which can be run to check if all settings for LaTeX are complete. It should return the pdf location if everything is ok. 

```
php app/console bobv:latex:test
```

If it detects problems, the errors are reported. 
