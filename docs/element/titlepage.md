# Title Page block
-------------------------------

The title page block defines a standard title page

### Constructor

The constructor takes a mandatory title, and optional subtitle, author and date.

```php
new TitlePage($title)
```

### Params

```
    'title'           => $title,
    'subtitle'        => $subtitle,
    'author'          => $author,
    'date'            => $date,

    'vspace'          => '2in',
    'vspace_subtitle' => '0.1in',

    'extra_commands'  => array(),
```

### Template

See [here](https://github.com/bobvandevijver/latex-bundle/blob/main/src/Resources/views/Element/titlepage.tex.twig).
