# Article base block
-------------------------------

The article base block defines a standard article document class for use.

### Constructor

The constructor takes a mandatory filename.

```php
new Article($filename)
```

### Params

```
    'lhead'          => '', // Top left header
    'chead'          => '', // Top center header
    'rhead'          => '', // Top right header
    'headheight'     => '12pt',

    'lfoot'          => $dateTime->format('d-m-Y h:m'), // Bottom left footer
    'cfoot'          => '', // Bottom center footer
    'rfoot'          => 'Page\ \thepage\ of\ \pageref{LastPage}', // Bottom right footer
    'footskip'       => '20pt',

    'topmargin'      => '-0.45in', // Some document margins
    'evensidemargin' => '0in',
    'oddsidemargin'  => '0in',
    'textwidth'      => '6.5in',
    'textheight'     => '9.0in',
    'headsep'        => '0.25in',

    'linespread'     => '1.1', // Line spacing

    'headrulewidth'  => '0.4pt', // Header size
    'footrulewidth'  => '0.4pt', // Footer size

    'parindent'      => '0pt', // Remove parindentation

    'secnumdepth'    => '0', // Remove section numbers

    'tocdepth'       => '2', // TOC depth

    'extra_commands' => array(), //Define extra commands if needed
    'packages'       => array(), // Define extra packages to use
```

### Add package

This class also has an `addPackage($package)` method. Use this if you need to include extra packages.

### Add dependency

This class also has an `addDependency($dependency)` method. Use this if you need to copy files from a certain dir to the compilation dir.

### Template

See [here](https://github.com/bobvandevijver/latex-bundle/blob/master/Resources/views/Base/article.tex.twig).