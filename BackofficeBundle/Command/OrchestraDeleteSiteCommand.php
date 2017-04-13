<?php

namespace OpenOrchestra\BackofficeBundle\Command;

use OpenOrchestra\Backoffice\Event\SiteCommandEvent;
use OpenOrchestra\Backoffice\SiteCommandEvents;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
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

        $io->comment('Check site');
        $site = $siteRepository->findOneBySiteId($siteId);
        if (!$site instanceof SiteInterface) {
            $io->error('Site '.$siteId.' not found');

            return 0;
        }

        if (false === $site->isDeleted()) {
            $io->error('Site '.$siteId.' should be soft deleted');

            //return 0;
        }

        $io->comment('Check usage in other sites');

        $nodeRepository = $this->getContainer()->get('open_orchestra_model.repository.node');
        $contentRepository = $this->getContainer()->get('open_orchestra_model.repository.content');
        $blockRepository = $this->getContainer()->get('open_orchestra_model.repository.block');
        $routeDocumentRepository = $this->getContainer()->get('open_orchestra_model.repository.route_document');

        $nodes = $nodeRepository->findWithUseReferences($siteId);
        $usedInNodes = $this->findUsageReferenceInOtherSite($siteId, $nodes);
        if (!empty($usedInNodes)) {
            $io->section('Usage of nodes in other sites');
            $this->displayUsedReferences($io, $usedInNodes);
            $io->error('You should remove usage of nodes before remove site '.$siteId);

            return 0;
        }

        $contents = $contentRepository->findWithUseReferences($siteId);

        $usedInContents = $this->findUsageReferenceInOtherSite($siteId, $contents);
        if (!empty($usedInContents)) {
            $io->section('Usage of contents in other sites');
            $this->displayUsedReferences($io, $usedInContents);
            $io->error('You should remove usage of contents before remove site '.$siteId);

            return 0;
        }

        $siteEvent = new SiteCommandEvent($site, $io);
        $dispatcher = $this->getContainer()->get('event_dispatcher');

        $dispatcher->dispatch(SiteCommandEvents::SITE_CHECK_HARD_DELETE, $siteEvent);

        /*$io->comment('Remove use references of nodes');
        $nodeClass = $this->getContainer()->getParameter('open_orchestra_model.document.node.class');
        $this->removeUseReferenceEntity($siteId, $nodeClass);

        $io->comment('Remove nodes');
        $nodeRepository->createQueryBuilder()->field('siteId')->equals($siteId)->remove()->getQuery()->execute();

        $io->comment('Remove route document');
        $routeDocumentRepository->createQueryBuilder()->field('siteId')->equals($siteId)->remove()->getQuery()->execute();

        $io->comment('Remove use references of contents');
        $contentClass = $this->getContainer()->getParameter('open_orchestra_model.document.content.class');
        $this->removeUseReferenceEntity($siteId, $contentClass);

        $io->comment('Remove contents');
        $contentRepository->createQueryBuilder()->field('siteId')->equals($siteId)->remove()->getQuery()->execute();

        $io->comment('Remove use references of blocks');
        $blockClass = $this->getContainer()->getParameter('open_orchestra_model.document.block.class');
        $this->removeUseReferenceEntity($siteId, $blockClass);

        $io->comment('Remove blocks');
        $blockRepository->createQueryBuilder()->field('siteId')->equals($siteId)->remove()->getQuery()->execute();
*/
        $dispatcher->dispatch(SiteCommandEvents::SITE_HARD_DELETE, $siteEvent);

        $io->comment('Remove site');
        $objectManager = $this->getContainer()->get('object_manager');
        $objectManager->persist($site);
        $objectManager->remove($site);
        $objectManager->flush();

        return 1;
    }

    /**
     * @param String $siteId
     * @param String $entityClass
     */
    protected function removeUseReferenceEntity($siteId, $entityClass)
    {
        $objectManager = $this->getContainer()->get('object_manager');
        $referenceManager = $this->getContainer()->get('open_orchestra_backoffice.reference.manager');
        $limit = 20;
        $countEntities = $objectManager->createQueryBuilder($entityClass)->getQuery()->count();
        for ($skip = 0; $skip < $countEntities; $skip += $limit) {
            $entities = $objectManager->createQueryBuilder($entityClass)
                ->field('siteId')->equals($siteId)
                ->sort('id', 'asc')
                ->skip($skip)
                ->limit($limit)
                ->getQuery()->execute();
            foreach ($entities as $entity) {
                $referenceManager->removeReferencesToEntity($entity);
            }
            $objectManager->clear();
        }
    }

    /**
     * @param $siteId
     * @param $entities
     *
     * @return array
     */
    protected function findUsageReferenceInOtherSite($siteId, $entities)
    {
        $usedOtherSite = array();
        $supportedEntities = array(BlockInterface::ENTITY_TYPE, ContentInterface::ENTITY_TYPE);
        /** @var UseTrackableInterface $entity */
        foreach ($entities as $entity) {
            $references = $entity->getUseReferences();
            $entityReferences = array(
                'entity' => $entity,
                'references' => array()
            );
            foreach ($references as $type => $reference) {
                if (in_array($type, $supportedEntities)) {
                    $referenceIds = array_keys($reference);
                    $repo = $this->getContainer()->get('open_orchestra_model.repository.' . $type);
                    foreach ($referenceIds as $referenceId) {
                        $referenceEntity = $repo->findById($referenceId);
                        if (
                            $siteId !== $referenceEntity->getSiteId()
                        ) {
                            $entityReferences['references'][$type][$referenceEntity->getId()] = $referenceEntity;
                        }
                    }
                }
            }
            $usedOtherSite[] = $entityReferences;

        }

        return $usedOtherSite;
    }

    /**
     * @param SymfonyStyle $io
     * @param array        $usedReferences
     */
    protected function displayUsedReferences(SymfonyStyle $io, array $usedReferences)
    {
        foreach ($usedReferences as $usedReference) {
            $entity = $usedReference['entity'];
            $io->comment('Entity Name: <info>'.$entity->getName(). '</info> Version: <info>'.$entity->getVersion(). '</info> Language: <info>'.$entity->getLanguage() . '</info> is used in :');
            foreach ($usedReference['references'] as $type => $entitiesReference) {
                switch ($type) {
                    case BlockInterface::ENTITY_TYPE:
                        $this->displayUsedInBlocks($io, $entitiesReference);
                        break;
                    case ContentInterface::ENTITY_TYPE:
                        $this->displayUsedInContent($io, $entitiesReference);
                        break;
                }
            }
            $io->newLine();
            $io->text('-----------------------------------------------------------');
        }
    }

    /**
     * @param SymfonyStyle  $io
     * @param array         $blocks
     */
    protected function displayUsedInBlocks(SymfonyStyle $io, array $blocks)
    {
        $io->text('    <comment>Blocks:</comment>');
        /** @var BlockInterface $block */
        foreach (    $blocks as $block) {
            $io->text('    *  Name: <info>'. $block->getLabel() . '</info> Language: <info>'.$block->getLanguage().'</info> Type <info>'.$block->getComponent().'</info> in site <info>' . $block->getSiteId() . '</info>');
        }
    }

    /**
     * @param SymfonyStyle  $io
     * @param array         $contents
     */
    protected function displayUsedInContent(SymfonyStyle $io, array $contents)
    {
        $io->text('    <comment>Contents:</comment>');
        /** @var ContentInterface $content */
        foreach ($contents as $content) {
            $io->text('    *  Name: <info>'. $content->getContentId() . '</info> Language: <info>'.$content->getLanguage().'</info> Version: <info>'.$content->getVersion().'</info> in site <info>' . $content->getSiteId() . '</info>');
        }
    }
}
