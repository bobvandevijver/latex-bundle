# Text block
-------------------------------

The text block defines a standard text block directly printed in the document

### Constructor

The constructor takes a mandatory custom text/command which will be directly printed in the LaTeX file.

```php
new Text($text)
```

### Params

```
    'text'           => $text,
    'extra_commands' => array(),
```

### Template

See [here](https://github.com/bobvandevijver/latex-bundle/blob/main/src/Resources/views/Element/text.tex.twig).
