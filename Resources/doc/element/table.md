# Table block
-------------------------------

The table block defines a tabularx environment which can be used to create most table structures

### Constructor

The constructor takes a mandatory rows array, in which the row types are specified: this can be like X, l r

```php
new Table($rows)
```

### Params

```
    'rows'           => $rows,
    'data'           => array(),
    'width'          => '\textwidth',
    'extra_commands' => array(),
```

### Functions

The table block defines four extra public functions:

```
    addTopRule()    //Add \toprule command in the table
    addMidRule()    //Add \midtule command in the table
    addBottomRule() //Add \bottomrule command in the table
    addRow(array()) //Add row data: every array element will be splitted with & and it will be ended with \\
```

### Template

See [here](https://github.com/bobvandevijver/latex-bundle/blob/master/Resources/views/Element/table.tex.twig).