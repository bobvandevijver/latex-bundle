<?php

namespace BobV\LatexBundle\Twig;

if (class_exists('Twig\Extension\AbstractExtension')) {
    class AbstractBobVLatexExtension extends \Twig\Extension\AbstractExtension
    {
    }
} else {
    class AbstractBobVLatexExtension extends \Twig_Extension
    {
    }
}
