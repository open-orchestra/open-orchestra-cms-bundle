<?php

namespace OpenOrchestra\GroupBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use OpenOrchestra\GroupBundle\Document\Perimeter;
use OpenOrchestra\WorkflowFunctionModelBundle\Document\WorkflowProfileCollection;

/**
 * Class AbstractLoadGroupV2Data
 */
abstract class AbstractLoadGroupV2Data extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param string        $type
     * @param array<string> $items
     *
     * @return Perimeter
     */
    protected function createPerimeter($type, array $items)
    {
        $perimeter = new Perimeter($type);

        foreach ($items as $item) {
            $perimeter->addItem($item);
        }

        return $perimeter;
    }

    /**
     * @param array<string> $profileReferences
     *
     * @return WorkflowProfileCollection
     */
    protected function createProfileCollection(array $profileReferences)
    {
        $profileCollection = new WorkflowProfileCollection();

        foreach ($profileReferences as $reference) {
            $profileCollection->addProfile($this->getReference($reference));
        }

        return $profileCollection;
    }
}
