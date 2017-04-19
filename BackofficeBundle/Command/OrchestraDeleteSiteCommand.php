<?php

namespace OpenOrchestra\BackofficeBundle\Command;

use OpenOrchestra\Backoffice\Event\SiteCommandEvent;
use OpenOrchestra\Backoffice\SiteCommandEvents;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
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

            return 0;
        }

        $siteEvent = new SiteCommandEvent($site, $io);
        $dispatcher = $this->getContainer()->get('event_dispatcher');

        $io->comment('Check usage in other sites');

        $this->checkUsage($siteId, $io);
        $dispatcher->dispatch(SiteCommandEvents::SITE_CHECK_HARD_DELETE, $siteEvent);

        $this->deleteEntities($siteId, $io);
        $dispatcher->dispatch(SiteCommandEvents::SITE_HARD_DELETE, $siteEvent);

        $io->comment('Remove site');
        $objectManager = $this->getContainer()->get('object_manager');
        $objectManager->persist($site);
        $objectManager->remove($site);
        $objectManager->flush();

        return 1;
    }

    /**
     * @param string       $siteId
     * @param SymfonyStyle $io
     */
    protected function checkUsage($siteId, SymfonyStyle $io)
    {
        $deleteSiteTools = $this->getContainer()->get('open_orchestra_backoffice.command.orchestra_delete_site_tools');

        $nodeRepository = $this->getContainer()->get('open_orchestra_model.repository.node');
        $nodes = $nodeRepository->findWithUseReferences($siteId);
        $usedInNodes = $deleteSiteTools->findUsageReferenceInOtherSite($siteId, $nodes);
        if (!empty($usedInNodes)) {
            $io->section('Usage of nodes in other sites');
            $deleteSiteTools->displayUsedReferences($io, $usedInNodes);
            throw new \RuntimeException('You should remove usage of nodes before remove site '.$siteId);
        }

        $contentRepository = $this->getContainer()->get('open_orchestra_model.repository.content');
        $contents = $contentRepository->findWithUseReferences($siteId);
        $usedInContents = $deleteSiteTools->findUsageReferenceInOtherSite($siteId, $contents);
        if (!empty($usedInContents)) {
            $io->section('Usage of contents in other sites');
            $deleteSiteTools->displayUsedReferences($io, $usedInContents);
            throw new \RuntimeException('You should remove usage of contents before remove site '.$siteId);
        }
    }

    /**
     * @param string       $siteId
     * @param SymfonyStyle $io
     */
    protected function deleteEntities($siteId, $io)
    {
        $deleteSiteTools = $this->getContainer()->get('open_orchestra_backoffice.command.orchestra_delete_site_tools');

        $io->comment('Remove use references of nodes');
        $nodeClass = $this->getContainer()->getParameter('open_orchestra_model.document.node.class');
        $deleteSiteTools->removeUseReferenceEntity($siteId, $nodeClass);

        $io->comment('Remove nodes');
        $nodeRepository = $this->getContainer()->get('open_orchestra_model.repository.node');
        $nodeRepository->createQueryBuilder()->field('siteId')->equals($siteId)->remove()->getQuery()->execute();

        $io->comment('Remove route document');
        $routeDocumentRepository = $this->getContainer()->get('open_orchestra_model.repository.route_document');
        $routeDocumentRepository->createQueryBuilder()->field('siteId')->equals($siteId)->remove()->getQuery()->execute();

        $io->comment('Remove use references of contents');
        $contentClass = $this->getContainer()->getParameter('open_orchestra_model.document.content.class');
        $deleteSiteTools->removeUseReferenceEntity($siteId, $contentClass);

        $io->comment('Remove contents');
        $contentRepository = $this->getContainer()->get('open_orchestra_model.repository.content');
        $contentRepository->createQueryBuilder()->field('siteId')->equals($siteId)->remove()->getQuery()->execute();

        $io->comment('Remove use references of blocks');
        $blockClass = $this->getContainer()->getParameter('open_orchestra_model.document.block.class');
        $deleteSiteTools->removeUseReferenceEntity($siteId, $blockClass);

        $io->comment('Remove blocks');
        $blockRepository = $this->getContainer()->get('open_orchestra_model.repository.block');
        $blockRepository->createQueryBuilder()->field('siteId')->equals($siteId)->remove()->getQuery()->execute();
    }
}
