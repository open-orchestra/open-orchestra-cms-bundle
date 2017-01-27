<?php

namespace OpenOrchestra\BackofficeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use OpenOrchestra\ModelInterface\Model\ReadSiteInterface;
use OpenOrchestra\Backoffice\Manager\AutoPublishManagerInterface;

/**
 * Class OrchestraunpublishElementCommand
 */
abstract class OrchestraUnpublishElementCommand extends ContainerAwareCommand
{
    /**
     * Configure command
     */
    protected function configure()
    {
        $this
            ->setName('orchestra:unpublish:'.$this->getElementType())
            ->setDescription('Unpublish all eligibles '.$this->getElementType().'s')
            ->addOption('siteId', null, InputOption::VALUE_REQUIRED, 'If set, will unpublish '.$this->getElementType().'s only for this site');
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
                $this->unpublishElements($site, $output);
            } else {
                $output->writeln("<error>No website found with siteId " . $siteId . ".</error>");

                return 1;
            }
        } else {
            $siteCollection = $this->getContainer()->get('open_orchestra_model.repository.site')->findByDeleted(false);
            if ($siteCollection) {
                foreach ($siteCollection as $site) {
                    $this->unpublishElements($site, $output);
                }
            }
        }

        $output->writeln("\n<info>Done.</info>");

        return 0;
    }

    /**
     * Call elements unpublication for $site
     *
     * @param ReadSiteInterface $site
     * @param OutputInterface   $output
     */
    protected function unpublishElements(ReadSiteInterface $site, OutputInterface $output)
    {
        $elementPublisherManager = $this->getContainer()->get($this->getManagerName());

        $output->writeln("\n<info>Unpublishing '.$this->getElementType().'s for siteId " . $site->getSiteId() . "</info>");

        $unpublishResult = $elementPublisherManager->unpublishElements($site);

        if (AutoPublishManagerInterface::ERROR_NO_PUBLISHED_STATUS == $unpublishResult) {
            $output->writeln("<error>There is no published status defined.</error>");

        } elseif (AutoPublishManagerInterface::ERROR_NO_UNPUBLISHED_STATUS == $unpublishResult) {
            $output->writeln("<error>There is no status defined to unpublish '.$this->getElementType().'s.</error>");

        } elseif (is_array($unpublishResult)) {
            foreach ($unpublishResult as $element) {
                $output->writeln("<comment>-> " . $element['name'] . " (v" . $element['version'] . " " . $element['language'] . ") unpublished</comment>");
            }
        }
    }
}
