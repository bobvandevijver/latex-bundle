# Custom element block
-------------------------------

The custom element block defines a simple print block. The statement is escaped.

### Constructor

The constructor takes a mandatory custom element which will be directly printed in the LaTeX file (after escapeing).

```php
new CustomElement($custom)
```

### Params

```
    'custom'           => $custom,
```

### Template

See [here](https://github.com/bobvandevijver/latex-bundle/blob/master/src/Resources/views/Element/custom_element.tex.twig).
