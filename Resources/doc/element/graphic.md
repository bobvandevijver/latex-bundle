# Graphic block
-------------------------------

The graphic block defines a standard include graphics block directly to include an image.

### Constructor

The constructor takes a mandatory image location. This needs to be te full path, or just the filename when set as dependency.

```php
new Graphic($location)
```

### Params

```
    'placement'      => 'ht!',
    'location'       => $graphic_location,
    'width'          => '\textwidth',
    'caption'        => $caption,
    'label'          => 'fig:' . basename($graphic_location),
    'extra_commands' => array(),
```

### Template

See [here](https://github.com/bobvandevijver/latex-bundle/blob/master/Resources/views/Element/graphic.tex.twig).