<?php

namespace OpenOrchestra\BackofficeBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\BaseApiModelBundle\Document\ApiClient;

/**
 * Class LoadApiClientData
 */
class LoadApiClientData implements FixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $testClient = new ApiClient();
        $testClient->setName('Test client');
        $testClient->setKey('test_key');
        $testClient->setSecret('test_secret');
        $testClient->setTrusted(true);

        $manager->persist($testClient);
        $manager->flush();
    }
}
