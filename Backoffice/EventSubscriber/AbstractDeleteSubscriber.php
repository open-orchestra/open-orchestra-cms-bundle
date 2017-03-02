<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OpenOrchestra\ModelInterface\Model\TrashItemInterface;

/**
 * Class AbstractDeleteSubscriber
 */
abstract class AbstractDeleteSubscriber implements EventSubscriberInterface
{
    protected $objectManager;
    protected $trashItemClass;

    /**
     * @param ObjectManager $objectManager
     * @param string        $trashItemClass
     */
    public function __construct(ObjectManager $objectManager, $trashItemClass)
    {
        $this->trashItemClass = $trashItemClass;
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $entityId
     * @param string $siteId
     * @param string $name
     * @param string $type
     */
    protected function createTrashItem($entityId, $siteId, $name, $type)
    {
        /** @var TrashItemInterface $trashItem */
        $trashItem = new $this->trashItemClass();
        $trashItem->setEntityId($entityId);
        $trashItem->setName($name);
        $trashItem->setType($type);
        $trashItem->setSiteId($siteId);

        $this->objectManager->persist($trashItem);
        $this->objectManager->flush($trashItem);
    }
}
