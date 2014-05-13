# Letter base block
-------------------------------

The letter base block defines a standard letter document class for use.

### Constructor

The constructor takes a mandatory filename.

```php
new Letter($filename)
```

### Params

```
    'pagenumber'     => 'false', // Whether to print pagenumbers from page 2 and forward
    'parskip'        => 'full',  // Spacing between paragraphs (full, half, ..)
    'fromalign'      => 'right', // Alignment of the from address
    'foldmarks'      => 'false', // Whether to print folding marks
    'addrfield'      => 'true',  // Whether to print the address field
    'paper'          => 'a4',    // Paper size

    'left'           => '2cm',   // Page margins
    'right'          => '2cm',
    'top'            => '1cm',
    'bottom'         => '2cm',

    'toaddrvpos'     => '3cm',   // Positioning
    'toaddrhpos'     => '2.5cm',
    'refvpos'        => '7.5cm',

    'date'           => $datetime->format('d-m-Y'),

    'extra_commands' => array(), // Define extra commands if needed
    'packages'       => array(), // Define extra packages to use
```

### Add package

This class also has an `addPackage($package)` method. Use this if you need to include extra packages.

### Add dependency

This class also has an `addDependency($dependency)` method. Use this if you need to copy files from a certain dir to the compilation dir.

### Template

See [here](https://github.com/bobvandevijver/latex-bundle/blob/master/Resources/views/Base/letter.tex.twig).