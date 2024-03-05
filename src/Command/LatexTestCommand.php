<?php

namespace Bobv\LatexBundle\Command;

use Bobv\LatexBundle\Generator\LatexGeneratorInterface;
use Bobv\LatexBundle\Latex\Base\Article;
use Bobv\LatexBundle\Latex\Element\CustomElement;
use Bobv\LatexBundle\Latex\Element\Text;
use Bobv\LatexBundle\Latex\Element\TitlePage;
use Bobv\LatexBundle\Latex\Element\TOC;
use Bobv\LatexBundle\Latex\Section\Box;
use Bobv\LatexBundle\Latex\Section\Section;
use Bobv\LatexBundle\Latex\Section\SubSection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LatexTestCommand extends Command
{
  public function __construct(private readonly LatexGeneratorInterface $latexGenerator) {
    parent::__construct();
  }

  protected function configure(): void {
    $this
        ->setName('bobv:latex:test')
        ->setDescription('Generate a test LaTeX file (+ pdf)')
        ->addArgument('no-pdf', InputArgument::OPTIONAL, 'Do not generate a PDF test file');
  }

  protected function execute(InputInterface $input, OutputInterface $output): int {
    $latex          = new Article('BobvLatexTest');
    $latex->addPackage('lipsum');
    $latex->addElement(new TitlePage('Bobv Latex Test', 'a subtitle', 'an author'));
    $latex->addElement(new TOC());

    $section1 = new Section('Test Title');
    $section1->addElement(new Text('Test page'));
    $section1->addElement(new CustomElement('\lipsum[9]'));
    $section1->addElement(new SubSection('Subsection test'));
    $section1->addElement(new CustomElement('\lipsum[2]'));
    $latex->addElement($section1);

    $section2 = new Section('Test Title which is not in TOC');
    $section2->setParam('includeTOC', false);
    $section2->addElement(new Text('Test page, but not in TOC'));
    $section2->addElement(new CustomElement('\lipsum[2]'));
    $section2->addElement(new SubSection('Subsection test'));
    $box = new Box();
    $box->addElement(new Text('\lipsum[1]'));
    $section2->addElement($box);
    $latex->addElement($section2);

    $generatedLocation = $this->latexGenerator->generate($latex);

    $output->writeln($generatedLocation);

    return Command::SUCCESS;
  }
}
