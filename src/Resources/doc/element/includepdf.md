# Include PDF block
-------------------------------

The include PDF block defines a standard includepdf command to include a PDF page in the document.

### Constructor

The constructor takes a mandatory pdf location. This needs to be te full path, or just the filename when set as dependency.

```php
new IncludePdf($fileLocation, $skipFirstWallpaper = true)
```

### Notes

* I've included a little latex hack for the first page. When the second construct argument is false, the first page is printed as background. By doing this, you will not get a newpage, which might by unwanted. Make sure to use the `wallpaper` package when using this feature!
* This element uses the pdfinfo command installed on your machine when using the wallpaper structure. For Debian, the command is located in the `poppler-utils` package.

### Params

```
    'totalPages'           => $totalPages,
    'skip_first_wallpaper' => $skipFirstWallpaper,
    'pages'                => $pages,
    'width'                => '\textwidth',
    'frame'                => 'false',
    'file_location'        => $file_location,
    'more_options'         => '',
    'y_offset'             => '0pt',
    'x_offset'             => '0pt',
    'wallpaper_scaling'    => '0.83'
```

### Template

See [here](https://github.com/bobvandevijver/latex-bundle/blob/master/Resources/views/Element/includepdf.tex.twig).