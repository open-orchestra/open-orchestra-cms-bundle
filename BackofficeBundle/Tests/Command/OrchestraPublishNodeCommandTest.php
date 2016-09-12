<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Command;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use Symfony\Component\Console\Application;
use OpenOrchestra\BackofficeBundle\Command\OrchestraPublishNodeCommand;

/**
 * Class OrchestraPublishNodeCommandTest
 */
class OrchestraPublishNodeCommandTest extends AbstractBaseTestCase
{
    /**
     * @var OrchestraPublishNodeCommand
     */
    protected $command;

    protected $container;
    protected $application;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->container = Phake::mock('Symfony\Component\DependencyInjection\Container');

        $this->command = new OrchestraPublishNodeCommand();
        $this->command->setContainer($this->container);

        $this->application = new Application();
        $this->application->add($this->command);
    }

    /**
     * Test presence and name
     */
    public function testPresenceAndName()
    {
        $command = $this->application->find('orchestra:publish:node');

        $this->assertInstanceOf('Symfony\Component\Console\Command\Command', $command);
    }

    /**
     * Test the definition
     */
    public function testDefinition()
    {
        $definition = $this->command->getDefinition();

        $this->assertTrue($definition->hasOption('siteId'));
    }
}
