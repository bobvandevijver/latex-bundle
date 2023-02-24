<?php

namespace Bobv\LatexBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see
 * {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
  /**
   * {@inheritDoc}
   */
  public function getConfigTreeBuilder() {
    $treeBuilder = new TreeBuilder('bobv_latex');

    $treeBuilder->getRootNode()
        ->children()
          ->arrayNode('escaping')
            ->addDefaultsIfNotSet()
            ->children()
              ->booleanNode('use_symfony_string')
                ->info('When set to true, the Symfony String component will be used to transliterate the remaining unicode characters')
                ->defaultFalse()
              ->end()
            ->end()
          ->end()
        ->end();

    return $treeBuilder;
  }
}
