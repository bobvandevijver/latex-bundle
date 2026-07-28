<?php

namespace Bobv\LatexBundle\DependencyInjection;

use Bobv\LatexBundle\Compiler\LatexCompilerInterface;
use Bobv\LatexBundle\Compiler\LocalLatexCompiler;
use Bobv\LatexBundle\Generator\LatexGenerator;
use Bobv\LatexBundle\Generator\LatexGeneratorInterface;
use Bobv\LatexBundle\Generator\LockedLatexGenerator;
use Bobv\LatexBundle\Twig\BobvLatexExtension;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\String\UnicodeString;

class Configuration implements ConfigurationInterface
{
  public function getConfigTreeBuilder(): TreeBuilder
  {
    $treeBuilder = new TreeBuilder('bobv_latex');

    $nodeBuilder = $treeBuilder->getRootNode()->children();

    $compilerNodeBuilder = $nodeBuilder
      ->arrayNode('compiler')
      ->addDefaultsIfNotSet()
      ->children();
    $this->setClassDefinition(
      $compilerNodeBuilder,
      'compiler',
      LocalLatexCompiler::class,
      LatexCompilerInterface::class,
    );

    $compilerNodeBuilder
      ->arrayNode('dependency_dirs')
      ->info('Top level directories that are used as linked dependency directories, relative to the project directory. Will only be used for the configure-proxy-acls command.')
      ->defaultValue([])
      ->prototype('scalar');

    $podmanProxyNodeBuilder = $compilerNodeBuilder
      ->arrayNode('podman_proxy')
      ->info('Configuration for the podman proxy compiler')
      ->addDefaultsIfNotSet()
      ->children();
    $podmanProxyNodeBuilder
      ->scalarNode('socket_path')->defaultValue('unix:///home/bobv-latex-pdflatex/podman_proxy.sock');

    $podmanNodeBuilder = $compilerNodeBuilder
      ->arrayNode('podman')
      ->info('Configuration for the podman compiler')
      ->addDefaultsIfNotSet()
      ->children();

    $podmanNodeBuilder
      ->scalarNode('homedir')
      ->info('The home directory used for podman')
      ->defaultValue('/home/bobv-latex-pdflatex');

    $podmanNodeBuilder
      ->scalarNode('binary_path')
      ->info('The path to the podman binary')
      ->defaultValue('podman');

    $podmanNodeBuilder
      ->scalarNode('image')
      ->info('The image used for the podman container')
      ->defaultValue('registry.drenso.dev/open/docker-images/pdflatex:latest');

    $localNodeBuilder = $compilerNodeBuilder
      ->arrayNode('local')
      ->info('Configuration for the local compiler')
      ->addDefaultsIfNotSet()
      ->children();

    $localNodeBuilder
      ->scalarNode('pdflatex_binary_path')
      ->info('The path to the pdflatex binary')
      ->defaultValue('pdflatex');

    $localNodeBuilder
      ->scalarNode('bibtex_binary_path')
      ->info('The path to the bibtex binary')
      ->defaultValue('bibtex');

    $generatorNodeBuilder = $nodeBuilder
      ->arrayNode('generator')
      ->addDefaultsIfNotSet()
      ->children();
    $this->setClassDefinition(
      $generatorNodeBuilder,
      'generator',
      LatexGenerator::class,
      LatexGeneratorInterface::class,
    );

    $lockedGeneratorNodeBuilder = $nodeBuilder
      ->arrayNode('locked_generator')
      ->addDefaultsIfNotSet()
      ->children();
    $this->setClassDefinition(
      $lockedGeneratorNodeBuilder,
      'locked generator',
      LockedLatexGenerator::class,
      LatexGeneratorInterface::class,
    );

    $nodeBuilder
      ->arrayNode('twig_extension')
      ->addDefaultsIfNotSet()
      ->children()
      ->scalarNode('class')
      ->defaultValue(BobvLatexExtension::class)
      ->info('The implementation used as twig extension.');

    $nodeBuilder
      ->scalarNode('cache_dir')
      ->info('The directory where the generated files will be cached (always placed in a BobvLatex subdirectory)')
      ->defaultValue('%kernel.cache_dir%');

    $nodeBuilder
      ->scalarNode('max_age')
      ->info('The maximum age of the cached files')
      ->defaultValue('-1 day');

    $nodeBuilder
      ->booleanNode('use_symfony_string')
      ->info('When set to true, the Symfony String component will be used to transliterate the remaining unicode characters')
      ->defaultFalse()
      ->validate()
      ->ifFalse(static fn (bool $value) => !$value || class_exists(UnicodeString::class))
      ->thenInvalid('The "symfony/string" component is required in order to process unicode escaping. Try running "composer require symfony/string".');

    return $treeBuilder;
  }

  /** @param class-string $class */
  private function setClassDefinition(NodeBuilder $node, string $usage, string $class, string $interface): void
  {
    $node
      ->scalarNode('class')
      ->defaultValue($class)
      ->info("The implementation used as $usage. Must implement $interface")
      ->validate()
      ->ifFalse(fn (string $value): bool => is_subclass_of($value, $interface))
      ->thenInvalid('Supplied class must implement ' . $interface);
  }
}
