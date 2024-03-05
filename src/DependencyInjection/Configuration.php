<?php

namespace Bobv\LatexBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
  public function getConfigTreeBuilder(): TreeBuilder {
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
