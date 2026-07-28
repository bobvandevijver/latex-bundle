<?php

namespace Bobv\LatexBundle\DependencyInjection;

use Bobv\LatexBundle\Command\LatexTestCommand;
use Bobv\LatexBundle\Compiler\LatexCompilerInterface;
use Bobv\LatexBundle\Compiler\LocalLatexCompiler;
use Bobv\LatexBundle\Compiler\PodmanLatexCompiler;
use Bobv\LatexBundle\Compiler\PodmanProxyLatexCompiler;
use Bobv\LatexBundle\Generator\LatexGeneratorInterface;
use Bobv\LatexBundle\Generator\LockedLatexGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class BobvLatexExtension extends ConfigurableExtension
{
  private const BASE_ID                    = 'bobv.latex.';
  private const SERVICE_COMPILER           = self::BASE_ID . 'compiler';
  private const SERVICE_GENERATOR          = self::BASE_ID . 'generator';
  private const SERVICE_LOCKED_GENERATOR   = self::BASE_ID . 'generator.locked';
  private const SERVICE_TWIG_EXTENSION     = self::BASE_ID . 'twig.extension';

  /** @param array<string, mixed> $mergedConfig */
  protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
  {
    $container
      ->register(LatexTestCommand::class)
      ->setArguments([
        new Reference(LatexGeneratorInterface::class),
      ])
      ->addTag('console.command');

    $compilerDefinition = $container
      ->register(self::SERVICE_COMPILER)
      ->setClass($mergedConfig['compiler']['class']);

    match ($compilerDefinition->getClass()) {
      LocalLatexCompiler::class => $compilerDefinition
        ->setArguments([
          $mergedConfig['compiler']['pdflatex_binary_path'],
          $mergedConfig['compiler']['bibtex_binary_path'],
        ]),
      PodmanLatexCompiler::class => $compilerDefinition
        ->setArguments([
          $mergedConfig['compiler']['podman_homedir'],
          $mergedConfig['compiler']['podman_binary_path'],
          $mergedConfig['compiler']['podman_image'],
        ]),
      PodmanProxyLatexCompiler::class => $compilerDefinition
        ->setArguments([
          $mergedConfig['compiler']['podman_proxy_sock'],
        ]),
      default => null,
    };
    $container->setAlias(LatexCompilerInterface::class, self::SERVICE_COMPILER);

    $container
      ->register(self::SERVICE_GENERATOR)
      ->setClass($mergedConfig['generator']['class'])
      ->setArguments([
        $mergedConfig['cache_dir'],
        new Parameter('kernel.environment'),
        new Reference('twig'),
        new Reference(self::SERVICE_COMPILER),
        $mergedConfig['max_age'],
      ]);
    $container->setAlias(LatexGeneratorInterface::class, self::SERVICE_GENERATOR);

    $container
      ->register(self::SERVICE_LOCKED_GENERATOR)
      ->setClass($mergedConfig['locked_generator']['class'])
      ->setArguments([
        new Reference(self::SERVICE_GENERATOR),
      ]);
    $container->setAlias(LockedLatexGeneratorInterface::class, self::SERVICE_LOCKED_GENERATOR);

    $container
      ->register(self::SERVICE_TWIG_EXTENSION)
      ->setClass($mergedConfig['twig_extension']['class'])
      ->setArguments([
        $mergedConfig['use_symfony_string'],
      ])
      ->addTag('twig.extension');
  }
}
