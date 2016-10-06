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
     * @param mixed  $entity
     * @param string $name
     * @param string $type
     */
    protected function createTrashItem($entity, $name, $type)
    {
        /** @var TrashItemInterface $trashItem */
        $trashItem = new $this->trashItemClass();
        $trashItem->setEntity($entity);
        $trashItem->setName($name);
        $trashItem->setType($type);
        $trashItem->setSiteId($entity->getSiteId());

        $this->objectManager->persist($trashItem);
        $this->objectManager->flush($trashItem);
    }
}
