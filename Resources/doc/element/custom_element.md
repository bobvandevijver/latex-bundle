# Custom element block
-------------------------------

The custom element block defines a simple print command block.

### Constructor

The constructor takes a mandatory custom command which will be directly printed in the LaTeX file.

```php
new CustomElement($custom)
```

### Params

```
    'custom'           => $custom,
```

### Template

See [here](https://github.com/bobvandevijver/latex-bundle/blob/master/Resources/views/Element/custom_element.tex.twig).