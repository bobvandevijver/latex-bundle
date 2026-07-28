<?php

namespace Bobv\LatexBundle\DependencyInjection;

use Bobv\LatexBundle\Cache\CacheService;
use Bobv\LatexBundle\Command\ConfigureProxyAclCommand;
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
  private const BASE_ID                  = 'bobv.latex.';
  private const SERVICE_COMPILER         = self::BASE_ID . 'compiler';
  private const SERVICE_GENERATOR        = self::BASE_ID . 'generator';
  private const SERVICE_LOCKED_GENERATOR = self::BASE_ID . 'generator.locked';
  private const SERVICE_TWIG_EXTENSION   = self::BASE_ID . 'twig.extension';

  /** @param array{
   *   compiler: array{
   *     class: string,
   *     podman_proxy: array{
   *       socket_path: string,
   *     },
   *     podman: array{
   *       homedir: string,
   *       binary_path: string,
   *       image: string,
   *     },
   *     local: array{
   *       pdflatex_binary_path: string,
   *       bibtex_binary_path: string,
   *     },
   *     dependency_dirs: list<string>,
   *   },
   *   generator: array{
   *     class: string,
   *   },
   *   locked_generator: array{
   *     class: string,
   *   },
   *   twig_extension: array{
   *     class: string,
   *   },
   *   cache_dir: string,
   *   max_age: string,
   *   use_symfony_string: bool,
   * } $mergedConfig
   */
  protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
  {
    $container
      ->register(LatexTestCommand::class)
      ->setArguments([
        new Reference(LatexGeneratorInterface::class),
      ])
      ->addTag('console.command');

    $container
      ->register(ConfigureProxyAclCommand::class)
      ->setArguments([
        new Reference(CacheService::class),
        new Parameter('kernel.project_dir'),
        $mergedConfig['compiler']['dependency_dirs'],
      ])
      ->addTag('console.command');

    $container
      ->register(CacheService::class)
      ->setArguments([
        $mergedConfig['cache_dir'],
      ]);

    $compilerDefinition = $container
      ->register(self::SERVICE_COMPILER)
      ->setClass($mergedConfig['compiler']['class']);

    match ($compilerDefinition->getClass()) {
      LocalLatexCompiler::class => $compilerDefinition
        ->setArguments([
          $mergedConfig['compiler']['local']['pdflatex_binary_path'],
          $mergedConfig['compiler']['local']['bibtex_binary_path'],
        ]),
      PodmanLatexCompiler::class => $compilerDefinition
        ->setArguments([
          $mergedConfig['compiler']['podman']['homedir'],
          $mergedConfig['compiler']['podman']['binary_path'],
          $mergedConfig['compiler']['podman']['image'],
        ]),
      PodmanProxyLatexCompiler::class => $compilerDefinition
        ->setArguments([
          $mergedConfig['compiler']['podman_proxy']['socket_path'],
        ]),
      default => null,
    };
    $container->setAlias(LatexCompilerInterface::class, self::SERVICE_COMPILER);

    $container
      ->register(self::SERVICE_GENERATOR)
      ->setClass($mergedConfig['generator']['class'])
      ->setArguments([
        new Reference(CacheService::class),
        new Reference(self::SERVICE_COMPILER),
        new Reference('twig'),
        new Parameter('kernel.environment'),
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
