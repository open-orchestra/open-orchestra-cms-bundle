<?php

namespace OpenOrchestra\BackofficeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use OpenOrchestra\ModelInterface\Model\ReadSiteInterface;
use OpenOrchestra\Backoffice\Manager\AutoPublishManagerInterface;

/**
 * Class OrchestraPublishElementCommand
 */
abstract class OrchestraPublishElementCommand extends ContainerAwareCommand
{
    /**
     * Configure command
     */
    protected function configure()
    {
        $this
            ->setName('orchestra:publish:'.$this->getElementType())
            ->setDescription('Publish all eligibles '.$this->getElementType().'s')
            ->addOption('siteId', null, InputOption::VALUE_REQUIRED, 'If set, will publish '.$this->getElementType().'s only for this site');
    }

    /**
     * Execute command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($siteId = $input->getOption('siteId')) {
            $site = $this->getContainer()->get('open_orchestra_model.repository.site')->findOneBySiteId($siteId);
            if ($site) {
                $this->publishElements($site, $output);
            } else {
                $output->writeln("<error>No website found with siteId " . $siteId . ".</error>");

                return 1;
            }
        } else {
            $siteCollection = $this->getContainer()->get('open_orchestra_model.repository.site')->findByDeleted(false);
            if ($siteCollection) {
                foreach ($siteCollection as $site) {
                    $this->publishElements($site, $output);
                }
            }
        }

        $output->writeln("\n<info>Done.</info>");

        return 0;
    }

    /**
     * Call elements publication for $site
     *
     * @param ReadSiteInterface $site
     * @param OutputInterface   $output
     */
    protected function publishElements(ReadSiteInterface $site, OutputInterface $output)
    {
        $elementPublisherManager = $this->getContainer()->get($this->getManagerName());

        $output->writeln("\n<info>Publishing ".$this->getElementType()."s for siteId " . $site->getSiteId() . "</info>");

        $publishResult = $elementPublisherManager->publishElements($site);

        if (AutoPublishManagerInterface::ERROR_NO_PUBLISH_FROM_STATUS == $publishResult) {
            $output->writeln("<error>There is no defined status to initiate the ".$this->getElementType()."s auto-publishing process.</error>");

        } elseif (AutoPublishManagerInterface::ERROR_NO_PUBLISHED_STATUS == $publishResult) {
            $output->writeln("<error>There is no published status defined.</error>");

        } elseif (is_array($publishResult)) {
            foreach ($publishResult as $element) {
                $output->writeln("<comment>-> " . $element['name'] . " (v" . $element['version'] . " " . $element['language'] . ") published</comment>");
            }
        }
    }
}
