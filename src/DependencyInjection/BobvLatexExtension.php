<?php

namespace Bobv\LatexBundle\DependencyInjection;

use Bobv\LatexBundle\Command\LatexTestCommand;
use Bobv\LatexBundle\Compiler\PodmanLatexCompiler;
use Bobv\LatexBundle\Compiler\LatexCompilerInterface;
use Bobv\LatexBundle\Compiler\LocalLatexCompiler;
use Bobv\LatexBundle\Generator\LatexGeneratorInterface;
use Bobv\LatexBundle\Generator\LockedLatexGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class BobvLatexExtension extends ConfigurableExtension
{
  private const BASE_ID                         = 'bobv.latex.';
  private const SERVICE_COMPILER                = self::BASE_ID . 'compiler';
  private const SERVICE_GENERATOR               = self::BASE_ID . 'generator';
  private const SERVICE_LOCKED_GENERATOR        = self::BASE_ID . 'generator.locked';
  private const SERVICE_TWIG_EXTENSION          = self::BASE_ID . 'twig.extension';
  private const PARAM_CACHE_DIR                 = self::BASE_ID . 'cache_dir';
  private const PARAM_MAX_AGE                   = self::BASE_ID . 'max_age';
  private const PARAM_USE_SYMFONY_STRING = self::BASE_ID . 'use_symfony_string';
  private const PARAM_PODMAN_HOMEDIR     = self::BASE_ID . 'compiler.podman.homedir';
  private const PARAM_PODMAN_BINARY_PATH   = self::BASE_ID . 'compiler.podman.binary.path';
  private const PARAM_PODMAN_IMAGE         = self::BASE_ID . 'compiler.podman.container';
  private const PARAM_PDFLATEX_BINARY_PATH = self::BASE_ID . 'compiler.pdflatex.binary.path';
  private const PARAM_BIBTEX_BINARY_PATH        = self::BASE_ID . 'compiler.bibtex.binary.path';

  /** @param array<string, mixed> $mergedConfig */
  protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
  {
    $container->setParameter(self::PARAM_CACHE_DIR, $mergedConfig['cache_dir']);
    $container->setParameter(self::PARAM_MAX_AGE, $mergedConfig['max_age']);
    $container->setParameter(self::PARAM_USE_SYMFONY_STRING, $mergedConfig['use_symfony_string']);

    $container->setParameter(self::PARAM_PODMAN_HOMEDIR, $mergedConfig['compiler']['podman_homedir']);
    $container->setParameter(self::PARAM_PODMAN_BINARY_PATH, $mergedConfig['compiler']['podman_binary_path']);
    $container->setParameter(self::PARAM_PODMAN_IMAGE, $mergedConfig['compiler']['podman_image']);
    $container->setParameter(self::PARAM_PDFLATEX_BINARY_PATH, $mergedConfig['compiler']['pdflatex_binary_path']);
    $container->setParameter(self::PARAM_BIBTEX_BINARY_PATH, $mergedConfig['compiler']['bibtex_binary_path']);

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
          new Parameter(self::PARAM_PDFLATEX_BINARY_PATH),
          new Parameter(self::PARAM_BIBTEX_BINARY_PATH),
        ]),
      PodmanLatexCompiler::class => $compilerDefinition
        ->setArguments([
          new Parameter(self::PARAM_PODMAN_HOMEDIR),
          new Parameter(self::PARAM_PODMAN_BINARY_PATH),
          new Parameter(self::PARAM_PODMAN_IMAGE),
          new Parameter(self::PARAM_PDFLATEX_BINARY_PATH),
          new Parameter(self::PARAM_BIBTEX_BINARY_PATH),
        ]),
      default => null,
    };
    $container->setAlias(LatexCompilerInterface::class, self::SERVICE_COMPILER);

    $container
      ->register(self::SERVICE_GENERATOR)
      ->setClass($mergedConfig['generator']['class'])
      ->setArguments([
        new Parameter(self::PARAM_CACHE_DIR),
        new Parameter('kernel.environment'),
        new Reference('twig'),
        new Reference(self::SERVICE_COMPILER),
        new Parameter(self::PARAM_MAX_AGE),
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
        new Parameter(self::PARAM_USE_SYMFONY_STRING),
      ])
      ->addTag('twig.extension');
  }
}
