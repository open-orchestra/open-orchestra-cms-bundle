<?php

namespace PHPOrchestra\BackofficeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
//use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OrchestraGenerateSitemapCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('orchestra:sitemap:generate')
            ->setDescription('Generate sitemaps')
            ->addOption('siteId', null, InputOption::VALUE_OPTIONAL, 'If set, will generate sitemap only for this id')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $text = "Génération de tous les sitemaps";
    
        if ($input->getOption('siteId')) {
            $text = 'Generation uniquement pour le site ' . $input->getOption('siteId');
        }

        $output->writeln($text);
    }
}