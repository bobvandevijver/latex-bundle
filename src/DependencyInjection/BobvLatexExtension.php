<?php

namespace Bobv\LatexBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\String\UnicodeString;

class BobvLatexExtension extends ConfigurableExtension
{
  protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
  {
    $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
    $loader->load('services.yml');

    if ($mergedConfig['escaping']['use_symfony_string'] === true) {
      // Test if Symfony string is available
      if (!class_exists(UnicodeString::class)) {
        throw new LogicException('The "symfony/string" component is required in order to process unicode escaping. Try running "composer require symfony/string".');
      }

      $container->setParameter('bobv.latex.use_symfony_string', true);
    }
  }
}
