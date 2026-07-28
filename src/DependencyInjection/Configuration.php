<?php

namespace Bobv\LatexBundle\DependencyInjection;

use Bobv\LatexBundle\Twig\BobvLatexExtension;
use Bobv\LatexBundle\Compiler\LatexCompilerInterface;
use Bobv\LatexBundle\Compiler\LocalLatexCompiler;
use Bobv\LatexBundle\Generator\LatexGenerator;
use Bobv\LatexBundle\Generator\LatexGeneratorInterface;
use Bobv\LatexBundle\Generator\LockedLatexGenerator;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

use Symfony\Component\String\UnicodeString;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

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
      ->scalarNode('podman_proxy_sock')->defaultValue('unix:///home/bobv-latex-pdflatex/podman_proxy.sock');

    $compilerNodeBuilder
      ->scalarNode('podman_homedir')->defaultValue('/home/bobv-latex-pdflatex');

    $compilerNodeBuilder
      ->scalarNode('podman_binary_path')->defaultValue('podman');

    $compilerNodeBuilder
      ->scalarNode('podman_image')->defaultValue('registry.drenso.dev/open/docker-images/pdflatex:latest');

    $compilerNodeBuilder
      ->scalarNode('pdflatex_binary_path')->defaultValue('pdflatex');

    $compilerNodeBuilder
      ->scalarNode('bibtex_binary_path')->defaultValue('bibtex');

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
      ->scalarNode('cache_dir')->defaultValue(param('kernel.cache_dir'));

    $nodeBuilder
      ->scalarNode('max_age')->defaultValue('-1 day');

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
