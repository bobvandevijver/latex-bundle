# Listing block
-------------------------------

The list block defines a standard itemize block directly printed in the document

### Constructor

The constructor takes a array which will be directly printed as items in the LaTeX file.

```php
new Listing($list)
```

### Params

```
    'list'           => $list,
    'extra_commands' => array(),
```

### Template

See [here](https://github.com/bobvandevijver/latex-bundle/blob/master/Resources/views/Element/listing.tex.twig).