# Standalone base block
-------------------------------

The standalone base block defines a standard standalone document class for use.

### Constructor

The constructor takes a mandatory filename.

```php
new Standalone($filename)
```

### Params

```
    'mode'           => 'crop',  // Define the standalone mode
    'border'         => '1pt',   // Content border
    
    'extra_commands' => array(), // Define extra commands if needed
    'packages'       => array(), // Define extra packages to use
```

### Add package

This class also has an `addPackage($package, $options = '')` method. Use this if you need to include extra packages.

### Add dependency

This class also has an `addDependency($dependency)` method. Use this if you need to copy files from a certain dir to the compilation dir.

### Template

See [here](https://github.com/bobvandevijver/latex-bundle/blob/master/Resources/views/Base/standalone.tex.twig).
