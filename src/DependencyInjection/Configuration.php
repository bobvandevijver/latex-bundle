<?php

namespace BobV\LatexBundle\DependencyInjection;

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

    if (method_exists($treeBuilder, 'getRootNode')) {
      $rootNode = $treeBuilder->getRootNode();
    } else {
      // for symfony/config 4.1 and older
      $rootNode = $treeBuilder->root('bobv_latex');
    }

    // Here you should define the parameters that are allowed to
    // configure your bundle. See the documentation linked above for
    // more information on that topic.

    $rootNode
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
