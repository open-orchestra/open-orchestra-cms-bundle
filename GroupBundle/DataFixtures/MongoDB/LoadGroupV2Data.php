<?php

namespace OpenOrchestra\GroupBundle\DataFixtures\MongoDB;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;
use OpenOrchestra\GroupBundle\Document\Group;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use OpenOrchestra\GroupBundle\Document\Perimeter;
use OpenOrchestra\WorkflowFunctionModelBundle\Document\WorkflowProfileCollection;

/**
 * Class LoadGroupV2Data
 */
class LoadGroupV2Data extends AbstractFixture implements OrderedFixtureInterface, OrchestraFunctionalFixturesInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $nodePerimeter = $this->createPerimeter(array(
            'root/fixture_page_legal_mentions',
            'root/fixture_page_contact'
        ));
        $nodeProfileCollection = $this->createProfileCollection(array('profile-Contributor'));

        $mediaPerimeter = $this->createPerimeter(array('first_images_folder'));
        $mediaProfileCollection = $this->createProfileCollection(array('profile-Contributor', 'profile-Validator'));

        $group = new Group('Group v2');
        $group->addLabel('en', 'Group v2');
        $group->addLabel('fr', 'Groupe v2');
        $group->setSite($this->getReference('site2'));
        $group->addWorkflowProfileCollection('Node', $nodeProfileCollection);
        $group->addWorkflowProfileCollection('Media', $mediaProfileCollection);
        $group->addPerimeter('Node', $nodePerimeter);
        $group->addPerimeter('Media', $mediaPerimeter);

        $manager->persist($group);
        $manager->flush();
    }

    /**
     * @param array<string> $paths
     *
     * @return Perimeter
     */
    protected function createPerimeter(array $paths)
    {
        $perimeter = new Perimeter();

        foreach ($paths as $path) {
            $perimeter->addPath($path);
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

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 612;
    }
}
