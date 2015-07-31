# Longtable block
-------------------------------

The longtable block defines a longtable environment which can be used to create long tables with automatic page breaks.

### Constructor

The constructor takes a mandatory rows array, in which the row types are specified: this can be like X, l r

```php
new LongTable($rows)
```

### Params

```
    'caption'        => NULL,
    'firsthead'      => NULL,
    'head'           => NULL,
    'foot'           => NULL,
    'lastfoot'       => NULL,
    'rows'           => $rows,
    'data'           => array(),
    'extra_commands' => array(),
```

### Functions

The table block defines two extra public functions:

```
    addRow(array()) //Add row data: every array element will be splitted with & and it will be ended with \\
```

### Template

See [here](https://github.com/bobvandevijver/latex-bundle/blob/master/Resources/views/Element/longtable.tex.twig).