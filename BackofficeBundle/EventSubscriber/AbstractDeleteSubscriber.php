<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

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
     */
    protected function createTrashItem($entity, $name)
    {
        /** @var TrashItemInterface $trashItem */
        $trashItem = new $this->trashItemClass();
        $trashItem->setEntity($entity);
        $trashItem->setName($name);

        $this->objectManager->persist($trashItem);
        $this->objectManager->flush($trashItem);
    }
}
