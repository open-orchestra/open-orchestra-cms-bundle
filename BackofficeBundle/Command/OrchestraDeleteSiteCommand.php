<?php

namespace OpenOrchestra\BackofficeBundle\Command;

use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\ModelInterface\Model\UseTrackableInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class OrchestraDeleteSiteCommand
 */
class OrchestraDeleteSiteCommand extends ContainerAwareCommand
{

    /**
     * Configure command
     */
    protected function configure()
    {
        $this
            ->setName('orchestra:site:delete')
            ->setDescription('Remove a site completely (node, media, content, ...)')
            ->addArgument('siteId', InputArgument::REQUIRED);
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
        $io = new SymfonyStyle($input, $output);

        $siteRepository = $this->getContainer()->get('open_orchestra_model.repository.site');
        $siteId = $input->getArgument('siteId');

        $site = $siteRepository->findOneBySiteId($siteId);
        if (!$site instanceof SiteInterface) {
            throw new \RuntimeException('Site '.$siteId.' not found');
        }

        /*if (false === $site->isDeleted()) {
            throw new \RuntimeException('Site '.$siteId.' should be soft deleted');
        }*/

        $nodeRepository = $this->getContainer()->get('open_orchestra_model.repository.node');
        $nodes = $nodeRepository->findWithUseReferences($siteId);
        $usageNodes = $this->findUsageReferenceInOtherSite($siteId, $nodes);

        dump($usageNodes);
    }

    /**
     * @param $siteId
     * @param $entities
     *
     * @return array
     */
    protected function findUsageReferenceInOtherSite($siteId, $entities)
    {
        $usageOtherSite = array();
        $supportedEntities = array(NodeInterface::ENTITY_TYPE, BlockInterface::ENTITY_TYPE, ContentInterface::ENTITY_TYPE);
        /** @var UseTrackableInterface $entity */
        foreach ($entities as $entity) {
            $references = $entity->getUseReferences();

            foreach ($references as $type => $reference) {
                if (in_array($type, $supportedEntities)) {
                    $referenceIds = array_keys($reference);
                    $repo = $this->getContainer()->get('open_orchestra_model.repository.' . $type);
                    foreach ($referenceIds as $referenceId) {
                        $referenceEntity = $repo->findById($referenceId);
                        if (
                            $siteId !== $referenceEntity->getSiteId()
                        ) {
                            $usageOtherSite[$type][$referenceEntity->getId()] = $referenceEntity;
                        }
                    }
                }
            }

        }

        return $usageOtherSite;
    }
}
