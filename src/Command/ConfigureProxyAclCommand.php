<?php

namespace Bobv\LatexBundle\Command;

use Bobv\LatexBundle\Cache\CacheService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Throwable;

class ConfigureProxyAclCommand extends Command
{
  private readonly Filesystem $filesystem;

  /** @param list<string> $dependencyDirs */
  public function __construct(
    private readonly CacheService $cacheService,
    private readonly string $projectDir,
    private readonly array $dependencyDirs,
  ) {
    parent::__construct();

    $this->filesystem = new Filesystem();
  }

  protected function configure(): void
  {
    $this
      ->setName('bobv:latex:configure-proxy-acls')
      ->setDescription('Configure proxy ACLs for the podman proxy user')
      ->addArgument('user', InputArgument::OPTIONAL, 'The user to configure proxy ACLs for', 'bobv-latex-pdflatex')
      ->addOption('dry-run', null, InputOption::VALUE_NONE, 'When provided the ACL commands will only be printed, but not executed.')
      ->addOption('output-only', null, InputOption::VALUE_NONE, 'Only output the commands');
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $sfStyle = new SymfonyStyle($input, $output);

    $cacheBasePath = $this->cacheService->getCacheBasePath(null);
    if (!$this->dirWithinProjectDir($cacheBasePath)) {
      $sfStyle->error('The cache directory must be inside the project directory for this command to be used');

      return self::FAILURE;
    }

    $this->cacheService->ensureCacheDirExists(null);

    /** @var bool $outputOnly */
    $outputOnly = $input->getOption('output-only');
    /** @var bool $dryRun */
    $dryRun = $input->getOption('dry-run');

    if (!$outputOnly && $dryRun) {
      $sfStyle->warning('Dry run mode enabled, no ACL commands will be executed');
    }

    /** @var string $user */
    $user = $input->getArgument('user');

    $setAcl = function (
      string $rights,
      string $directory,
      bool $recursive,
    ) use ($sfStyle, $outputOnly, $dryRun, $user) {
      $recursive = $recursive ? 'R' : '';
      $commands  = [
        ['setfacl', "-{$recursive}nm", "u:$user:$rights", $directory],
        ['setfacl', "-d{$recursive}nm", "u:$user:$rights", $directory],
      ];

      foreach ($commands as $command) {
        $stringifiedCommand = implode(' ', $command);
        if ($outputOnly) {
          $sfStyle->writeln($stringifiedCommand);
        } elseif ($dryRun) {
          $sfStyle->writeln('<info>Would run command: ' . $stringifiedCommand . '</info>');
        } else {
          $sfStyle->writeln('<info>Running command: ' . $stringifiedCommand . '</info>');
          (new Process($command))->mustRun();
        }
      }
    };

    try {
      $setAcl('rwX', $cacheBasePath, true);

      $parentDir = dirname($cacheBasePath);
      while ($this->dirWithinProjectDir($parentDir)) {
        $setAcl('rX', $parentDir, false);
        $parentDir = dirname($parentDir);
      }

      foreach ($this->dependencyDirs as $dependencyDir) {
        $dependencyDir = $this->projectDir . DIRECTORY_SEPARATOR . $dependencyDir;
        if (!$this->filesystem->exists($dependencyDir)) {
          $sfStyle->warning("Dependency directory does not exist, skipping ($dependencyDir)");
          continue;
        }

        while ($this->dirWithinProjectDir($dependencyDir) && $dependencyDir !== $this->projectDir) {
          $setAcl('rX', $dependencyDir, true);
          $dependencyDir = dirname($dependencyDir);
        }
      }
    } catch (Throwable $e) {
      $sfStyle->error('Failed to configure proxy ACLs: ' . $e->getMessage());

      return self::FAILURE;
    }

    return self::SUCCESS;
  }

  public function dirWithinProjectDir(string $dir): bool
  {
    return str_starts_with($dir, $this->projectDir);
  }
}
