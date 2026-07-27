# Letter block
-------------------------------

The letter block defines a standard letter block directly printed in the document

### Constructor

The constructor takes three mandatory params which will be directly printed in the LaTeX file as address, opening and text.

```php
new Text($text)
```

### Params

```
    'address'        => $address, // Address
    'opening'        => $opening, // Opening of the letter
    'text'           => $text,    // Content of the letter

    'extra_commands' => array(),  // Define extra commands if needed
```

### Template

See [here](https://github.com/bobvandevijver/latex-bundle/blob/main/src/Resources/views/Element/letter.tex.twig).
