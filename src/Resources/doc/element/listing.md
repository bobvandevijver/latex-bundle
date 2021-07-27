# Listing block
-------------------------------

The list block defines a standard itemize block directly printed in the document

### Constructor

The constructor takes an array which will be directly printed as items in the LaTeX file. When the
second parameter is set to true, it will use an 'enumerate' instead of the 'listing' environment.

```php
new Listing($list)
new Listing($list, false)
```

### Params

```
    'list'           => $list,
    'extra_commands' => array(),
    'enumerate'      => $enumerate
```

### Template

See [here](https://github.com/bobvandevijver/latex-bundle/blob/master/src/Resources/views/Element/listing.tex.twig).
