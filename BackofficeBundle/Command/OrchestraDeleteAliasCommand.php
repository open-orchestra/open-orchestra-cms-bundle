<?php

namespace OpenOrchestra\BackofficeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class OrchestraDeleteAliasCommand
 */
class OrchestraDeleteAliasCommand extends ContainerAwareCommand
{
    /**
     * Configure command
     */
    protected function configure()
    {
        $this
            ->setName('orchestra:alias:delete')
            ->setDescription('Remove an alias')
            ->addArgument('aliasId', InputArgument::REQUIRED);
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
        $aliasId = $input->getArgument('aliasId');
        $limit = 20;

        $objectManager = $this->getContainer()->get('object_manager');
        $siteClass = $this->getContainer()->getParameter('open_orchestra_model.document.site.class');
        $nodeClass = $this->getContainer()->getParameter('open_orchestra_model.document.node.class');

        $site = $objectManager->getRepository($siteClass)
            ->findOneBy(array(
                'aliases.'.$aliasId => array('$exists' => true)
                )
            );

        if($site instanceof $siteClass) {
            $aliases = $site->getAliases();
            if ($aliases->containsKey($aliasId)) {
                $siteId = $site->getSiteId();
                $language = $aliases[$aliasId]->getLanguage();
                $lastAlias = true;
                foreach ($aliases as $key => $alias) {
                    if ($alias->getLanguage() == $language && $key != $aliasId) {
                        $lastAlias = false;
                    }
                }
            }

            if ($lastAlias) {
                $io->comment('Remove use references of nodes');

                $deleteSiteTools = $this->getContainer()->get('open_orchestra_backoffice.command.orchestra_delete_site_tools');

                $nodes = $objectManager->createQueryBuilder($nodeClass)
                    ->field('siteId')->equals($siteId)
                    ->field('language')->equals($language)
                    ->getQuery()->execute();

                $usedInNodes = $deleteSiteTools->findUsageReferenceInOtherSite($siteId, $nodes);
                if (!empty($usedInNodes)) {
                    $io->section('Usage of nodes in other sites');
                    $deleteSiteTools->displayUsedReferences($io, $usedInNodes);
                    throw new \RuntimeException('You should remove usage of nodes before remove alias ' . $aliasId);
                }

                $count = count($nodes);

                for ($skip = 0; $skip < $count; $skip += $limit) {
                    $nodes = $objectManager->createQueryBuilder($nodeClass)
                        ->field('siteId')->equals($siteId)
                        ->field('language')->equals($language)
                        ->sort('id', 'asc')
                        ->skip($skip)
                        ->limit($limit)
                        ->getQuery()->execute();
                    foreach ($nodes as $node) {
                        $this->getContainer()->get('open_orchestra_backoffice.reference.manager')->removeReferencesToEntity($node);
                    }
                    $objectManager->clear();
                }

                $io->comment('Remove nodes');
                $objectManager->createQueryBuilder($nodeClass)
                    ->field('siteId')->equals($siteId)
                    ->field('language')->equals($language)
                    ->remove()->getQuery()->execute();
            }

            $site->removeAlias($aliases->get($aliasId));
            $objectManager->persist($site);
            $objectManager->flush();

        }
    }
}
