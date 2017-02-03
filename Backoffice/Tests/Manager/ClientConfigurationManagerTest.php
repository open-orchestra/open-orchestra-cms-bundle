<?php

namespace OpenOrchestra\Backoffice\Tests\Manager;

use OpenOrchestra\Backoffice\Manager\ClientConfigurationManager;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;

/**
 * Class ClientConfigurationManagerTest
 */
class ClientConfigurationManagerTest extends AbstractBaseTestCase
{
    /**
     * @var ClientConfigurationManager
     */
    protected $manager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->manager = new ClientConfigurationManager();
    }

    /**
     * Test add client configuration
     */
    public function testAddClientConfiguration()
    {
        $key = 'test';
        $value = 'fakeValue';
        $this->manager->addClientConfiguration($key, $value);

        $clientConfiguration = $this->manager->getClientConfiguration();
        $this->assertArrayHasKey($key, $clientConfiguration);
        $this->assertSame($value, $clientConfiguration[$key]);
    }
}
