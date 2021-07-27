# Escaped Text block
-------------------------------

The escaped text block defines a standard text block (but fully escaped) directly printed in the document

### Constructor

The constructor takes a mandatory custom text/command which will be directly printed in the LaTeX file.

```php
new EscapedText($text)
```

### Params

```
    'text'           => $text,
    'extra_commands' => array(),
```

### Template

See [here](https://github.com/bobvandevijver/latex-bundle/blob/master/src/Resources/views/Element/escaped_text.tex.twig).
