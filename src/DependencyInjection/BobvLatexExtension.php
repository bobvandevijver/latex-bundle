<?php

namespace Bobv\LatexBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\String\UnicodeString;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class BobvLatexExtension extends Extension
{
  /**
   * {@inheritDoc}
   */
  public function load(array $configs, ContainerBuilder $container) {
    $configuration = new Configuration();
    $config        = $this->processConfiguration($configuration, $configs);

    $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
    $loader->load('services.yml');

    if ($config['escaping']['use_symfony_string'] === true) {
      // Test if Symfony string is available
      if (!class_exists(UnicodeString::class)) {
        throw new LogicException('The "symfony/string" component is required in order to process unicode escaping. Try running "composer require symfony/string".');
      }

      $container->setParameter('bobv.latex.use_symfony_string', true);
    }
  }
}
