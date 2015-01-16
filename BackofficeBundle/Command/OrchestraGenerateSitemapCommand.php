<?php

namespace PHPOrchestra\BackofficeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
//use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OrchestraGenerateSitemapCommand extends ContainerAwareCommand
{
    /**
     * Configure command
     */
    protected function configure()
    {
        $this
            ->setName('orchestra:sitemap:generate')
            ->setDescription('Generate sitemaps')
            ->addOption('siteId', null, InputOption::VALUE_OPTIONAL, 'If set, will only generate sitemap for this site');
    }

    /**
     * Execute command
     * 
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($siteId = $input->getOption('siteId')) {
            $this->generateSitemap($siteId, $output);
        } else {
            $sites = array('1', '3', '6', '9');
            foreach ($sites as $siteId) {
                $this->generateSitemap($siteId, $output);
            }
        }

        $output->writeln("<info>Done.</info>");
    }

    /**
     * Generate sitemap for siteId
     * 
     * @param string          $siteId
     * @param OutputInterface $output
     */
    protected function generateSitemap($siteId, OutputInterface $output)
    {
        $output->writeln("<info>Generating sitemap for siteId " . $siteId . "</info>");

        $filename = 'sitemap.' . $siteId . '.xml';
        $text = 'random xml content';

        file_put_contents('web/' . $filename, $text);
        $output->writeln("<comment>-> " . $filename . " generated</comment>\n");

        return true;
    }
}