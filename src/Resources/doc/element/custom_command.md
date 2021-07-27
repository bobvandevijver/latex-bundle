# Custom command block
-------------------------------

The custom command block defines a simple print command block. The command is not escaped.

### Constructor

The constructor takes a mandatory custom command which will be directly printed in the LaTeX file.

```php
new CustomCommand($custom)
```

### Params

```
    'custom'           => $custom,
```

### Template

See [here](https://github.com/bobvandevijver/latex-bundle/blob/master/src/Resources/views/Element/custom_command.tex.twig).
