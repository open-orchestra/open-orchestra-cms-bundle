<?php

namespace OpenOrchestra\BackofficeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use OpenOrchestra\ModelInterface\Model\ReadSiteInterface;
use OpenOrchestra\Backoffice\Manager\NodePublisherInterface;

/**
 * Class OrchestraPublishNodeCommand
 */
class OrchestraPublishNodeCommand extends ContainerAwareCommand
{
    /**
     * Configure command
     */
    protected function configure()
    {
        $this
            ->setName('orchestra:publish:node')
            ->setDescription('Publish all eligibles nodes')
            ->addOption('siteId', null, InputOption::VALUE_REQUIRED, 'If set, will publish nodes only for this site');
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
                $this->publishNodes($site, $output);
            } else {
                $output->writeln("<error>No website found with siteId " . $siteId . ".</error>");

                return 1;
            }
        } else {
            $siteCollection = $this->getContainer()->get('open_orchestra_model.repository.site')->findByDeleted(false);
            if ($siteCollection) {
                foreach ($siteCollection as $site) {
                    $this->publishNodes($site, $output);
                }
            }
        }

        $output->writeln("\n<info>Done.</info>");

        return 0;
    }

    /**
     * Call nodes publication for $site
     *
     * @param ReadSiteInterface $site
     * @param OutputInterface   $output
     */
    protected function publishNodes(ReadSiteInterface $site, OutputInterface $output)
    {
        $nodePublisherManager = $this->getContainer()->get('open_orchestra_cms.manager.node_publisher');

        $output->writeln("\n<info>Publishing nodes for siteId " . $site->getSiteId() . "</info>");

        $publishResult = $nodePublisherManager->publishNodes($site);

        if (NodePublisherInterface::ERROR_NO_PUBLISH_FROM_STATUS == $publishResult) {
            $output->writeln("<error>There is no defined status to initiate the nodes auto-publishing process.</error>");

        } elseif (NodePublisherInterface::ERROR_NO_PUBLISHED_STATUS == $publishResult) {
            $output->writeln("<error>There is no published status defined.</error>");

        } elseif (is_array($publishResult)) {
            foreach ($publishResult as $node) {
                $output->writeln("<comment>-> " . $node['name'] . " (v" . $node['version'] . " " . $node['language'] . ") published</comment>");
            }
        }
    }
}
