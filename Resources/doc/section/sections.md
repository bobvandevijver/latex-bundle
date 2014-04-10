# (sub)(sub)section block
-------------------------------

The (sub)(sub)section block defines the standard LaTeX (sub)(sub)sections

### Constructor

The constructor takes a not mandatory section title.

```php
new Section($sectionTitle = '')
new SubSection($subsectionTitle = '')
new SubSubSection($subsubsectionTitle = '')
```

### Params

```
    'sectionTitle' => $sectionTitle,
    'includeTOC' => true,

    'newpage' => true, // Standard a section starts on a new page

    'extra_commands' => array(), // Define extra commands at the begin of the section
```

For the sub and subsub section the `newpage` param is standard false

### Template

See [here](https://github.com/bobvandevijver/latex-bundle/blob/master/Resources/views/Element/section.tex.twig).
See [here](https://github.com/bobvandevijver/latex-bundle/blob/master/Resources/views/Element/subsection.tex.twig).
See [here](https://github.com/bobvandevijver/latex-bundle/blob/master/Resources/views/Element/subsubsection.tex.twig).