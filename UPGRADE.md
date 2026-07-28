# Bobv Latex Bundle - Upgrade notes
-------------------------------------

## Upgrade from 6.x to 7.x

This is a major release, and contains breaking changes. Please read carefully.

### Configuration

The configuration has been rebuilt, and parameters have changed. If you were using parameters (starting with `bobv.latex.`) please migrate to modern bundle configuration. Every option that was available before can still be set.

### Generator

The following methods have been dropped from the `LatexGeneratorInterface` in favour of passing an options DTO to the generate methods. This makes the Generator instance stateless.

- `setForceRegenerate`
- `setCacheDir`
- `setMaxAge`
- `setTimeout`

The `$compileOptions` argument on method `generatePdf` has been replaced with the same options DTO. Do note that the following keys from `$pdflatexArgs` have been replaced with dedicated DTO options:

- `shell-escape`
- `openin_any`
- `openout_any`

The `generateLatex` method must now always be passed a `LatexBaseInterface` instance.

The `generatePdf` method no longer accepts a `string` as file location, it must now be passed a `TexFileInfo` instance.

The `generate*` functions now return an instance of `TexFileInfo` or `PdfFileInfo`, depending on the method. You can call `getTexLocation()` or `getPdfLocation()` on the returned instance to get the full file path.

### BibTeX

With this release, setting the BibTex path is no longer enough to trigger a BibTeX build. You will need to explicitly set `runBibtex` to `true` in the `GeneratorOptions` DTO.

## Upgrade from 5.x to 6.x

This release marks the addition of a possibly breaking safety feature, namely passing `-cnf-line="openin_any=p"` and `-cnf-line="openout_any=p"` by default to the `pdflatex` command.

## Upgrade from 4.x to 5.x

This release focused on adding typings to methods. Most should be straight forward to adjust, and will only affect you if you were extending the internals.

## Upgrade from 3.x to 4.x

This major only dropped support for older Symfony versions.

## Upgrade from 2.x to 3.x

The namespace has changed from `BobV\LatexBundle` to `Bobv\LatexBundle` (note the capital `V`) in order to be consistent over with stringification of the bundle name. This means you will have to change your usages where applicatable:

- PHP namespace: `BobV\LatexBundle` => `Bobv\LatexBundle`
- Twig bundle: `@BobVLatex` => `@BobvLatex`
- Configuration key: `bob_v_latex` => `bobv_latex`
- Couple of classes within `Bobv\LatexBundle`:
  - `BobVLatexBundle` => `BobvLatexBundle`  
  - `DependencyInjection/BobVLatexExtension` => `DependencyInjection/BobvLatexExtension`  
  - `Twig/AbstractBobVLatexExtension` => `Twig/AbstractBobvLatexExtension`
  - `Twig/BobVLatexExtension` => `Twig/BobvLatexExtension`

## Upgrade from 1.x to 2.x

Symfony 2 and PHP 5 are no longer supported, so make sure to upgrade your systems.

## Upgrade from <=0.5 to 0.6

The `addPackage` call is changed to also accept an options string for the package. To allow this change, the base templates have changed. There will be no problems when you did not overwrite the base article, book or letter templates.  

The packages are now contained in an array, where previously the were simply a string. 
```php
array(
    'p' => $package,
    'o' => $options
);
```

You should adjust your custom templates accordingly:
```twig
{% for package in packages -%}
  \usepackage[ {{- package.o -}} ]{ {{- package.p -}} }
{%- endfor %}
```
