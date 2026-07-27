# minipage block
-------------------------------

The minipage block defines a standard LaTeX minipage

### Constructor

The constructor takes no arguments

```php
new MiniPage()
```

### Params

```
    'width'          => '\textwidth', // Width of the minipage

    'newpage'        => false, // Standard a section starts on a new page

    'extra_commands' => array(), // Define extra commands at the begin of the section
```

### Template

See [here](https://github.com/bobvandevijver/latex-bundle/blob/main/src/Resources/views/Element/minipage.tex.twig).
