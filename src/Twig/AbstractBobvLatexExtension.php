<?php

namespace Bobv\LatexBundle\Twig;

if (class_exists('Twig\Extension\AbstractExtension')) {
    class AbstractBobvLatexExtension extends \Twig\Extension\AbstractExtension
    {
    }
} else {
    class AbstractBobvLatexExtension extends \Twig_Extension
    {
    }
}
