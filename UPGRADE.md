# BobV Latex Bundle - Upgrade notes
-------------------------------------

[![Join the chat at https://gitter.im/bobvandevijver/latex-bundle](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/bobvandevijver/latex-bundle?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

### Upgrade from <=0.5 to 0.6

The `addPackage` call is changed to also accept an options string for the package. To allow this change, the base templates have changed. There will be no problems when you did not overwrite the base article, book or letter templates.  

The packages are now contained in an array, where previously the were simply a string. 
```php
array(
    'p' => $package,
    'o' => $options
);
```

You should adjust your custom templates accordingly:
```twig
{% for package in packages -%}
  \usepackage[ {{- package.o -}} ]{ {{- package.p -}} }
{%- endfor %}
```