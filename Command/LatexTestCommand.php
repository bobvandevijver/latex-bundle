<?php

namespace BobV\LatexBundle\Command;

use BobV\LatexBundle\Latex\Base\Article;
use BobV\LatexBundle\Latex\Element\Text;
use BobV\LatexBundle\Latex\Element\Title;
use BobV\LatexBundle\Latex\Element\TOC;
use BobV\LatexBundle\Latex\Section\Section;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Application;

class LatexTestCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('bobv:latex:test')
            ->setDescription('Generate a test LaTeX file (+ pdf)')
            ->addArgument('no-pdf', InputArgument::OPTIONAL, 'Do not generate a PDF test file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
      $latexGenerator = $this->getContainer()->get('bobv.latex.generator');
      $latex = new Article('BobVLatexTest');
      $latex->addElement(new Title('BobV Latex Test'));
      $latex->addElement(new TOC());
      $section1 = new Section('Test Title');
      $section1->addElement(new Text('Test page'));
      $latex->addElement($section1);
      $generatedLocation = $latexGenerator->generate($latex);

      $output->writeln($generatedLocation);

    }
}