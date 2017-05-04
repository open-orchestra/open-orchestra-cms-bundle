<?php

namespace OpenOrchestra\Backoffice\Command;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\Backoffice\Reference\ReferenceManager;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Model\UseTrackableInterface;
use OpenOrchestra\ModelInterface\Repository\BlockRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class OrchestraDeleteSiteTools
 */
class OrchestraDeleteSiteTools
{
    protected $blockRepository;
    protected $contentRepository;
    protected $objectManager;
    protected $referenceManager;

    /**
     * @param ContentRepositoryInterface $contentRepository
     * @param BlockRepositoryInterface   $blockRepository
     * @param ObjectManager              $objectManager
     * @param ReferenceManager           $referenceManager
     */
    public function __construct(
        ContentRepositoryInterface $contentRepository,
        BlockRepositoryInterface $blockRepository,
        ObjectManager $objectManager,
        ReferenceManager $referenceManager
    ) {
        $this->blockRepository = $blockRepository;
        $this->contentRepository = $contentRepository;
        $this->objectManager = $objectManager;
        $this->referenceManager = $referenceManager;
    }

    /**
     * @param $siteId
     * @param $entities
     *
     * @return array
     */
    public function findUsageReferenceInOtherSite($siteId, $entities)
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
            if (!empty($references)) {
                foreach ($references as $type => $reference) {
                    if (in_array($type, $supportedEntities)) {
                        $referenceIds = array_keys($reference);
                        $repo = $this->getRepositoryByType($type);
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
        }

        return $usedOtherSite;
    }

    /**
     * @param SymfonyStyle $io
     * @param array        $usedReferences
     */
    public function displayUsedReferences(SymfonyStyle $io, array $usedReferences)
    {
        foreach ($usedReferences as $usedReference) {
            $entity = $usedReference['entity'];
            $io->comment('Entity <info>'.$entity->getName(). ' is used in :');
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
     * @param String $siteId
     * @param String $entityClass
     */
    public function removeUseReferenceEntity($siteId, $entityClass)
    {
        $limit = 20;
        $countEntities = $this->objectManager->createQueryBuilder($entityClass)->getQuery()->count();
        for ($skip = 0; $skip < $countEntities; $skip += $limit) {
            $entities = $this->objectManager->createQueryBuilder($entityClass)
                ->field('siteId')->equals($siteId)
                ->sort('id', 'asc')
                ->skip($skip)
                ->limit($limit)
                ->getQuery()->execute();
            foreach ($entities as $entity) {
                $this->referenceManager->removeReferencesToEntity($entity);
            }
            $this->objectManager->clear();
        }
    }

    /**
     * @param string $type
     *
     * @return mixed
     */
    protected function getRepositoryByType($type)
    {
        if (BlockInterface::ENTITY_TYPE === $type) {
            return $this->blockRepository;
        }
        if (ContentInterface::ENTITY_TYPE === $type) {
            return $this->contentRepository;
        }
        throw new \InvalidArgumentException($type . "ins't supported");
    }

    /**
     * @param SymfonyStyle  $io
     * @param array         $blocks
     */
    protected function displayUsedInBlocks(SymfonyStyle $io, array $blocks)
    {
        $io->text('    <comment>Blocks:</comment>');
        /** @var BlockInterface $block */
        foreach ($blocks as $block) {
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
