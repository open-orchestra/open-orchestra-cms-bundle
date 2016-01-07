<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Command;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\BackofficeBundle\Command\OrchestraGenerateBlockCommand;
use Symfony\Component\Console\Application;

/**
 * Class OrchestraGenerateBlockCommandTest
 */
class OrchestraGenerateBlockCommandTest extends AbstractBaseTestCase
{
    /**
     * @var OrchestraGenerateBlockCommand
     */
    protected $command;

    protected $container;
    protected $application;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->container = $this->container = Phake::mock('Symfony\Component\DependencyInjection\Container');

        $this->command = new OrchestraGenerateBlockCommand();
        $this->command->setContainer($this->container);

        $this->application = new Application();
        $this->application->add($this->command);
    }

    /**
     * Test presence and name
     */
    public function testPresenceAndName()
    {
        $command = $this->application->find('orchestra:generate:block');

        $this->assertInstanceOf('Symfony\Component\Console\Command\Command', $command);
    }

    /**
     * Test the definition
     */
    public function testDefinition()
    {
        $definition = $this->command->getDefinition();

        $this->assertTrue($definition->hasOption('block-name'));
        $this->assertTrue($definition->hasOption('form-generator-dir'));
        $this->assertTrue($definition->hasOption('form-generator-conf'));
        $this->assertTrue($definition->hasOption('form-generator-namespace'));
        $this->assertTrue($definition->hasOption('front-display-dir'));
        $this->assertTrue($definition->hasOption('front-display-conf'));
        $this->assertTrue($definition->hasOption('front-display-namespace'));
        $this->assertTrue($definition->hasOption('backoffice-icon-dir'));
        $this->assertTrue($definition->hasOption('backoffice-icon-conf'));
        $this->assertTrue($definition->hasOption('backoffice-icon-namespace'));
        $this->assertTrue($definition->hasOption('backoffice-display-dir'));
        $this->assertTrue($definition->hasOption('backoffice-display-conf'));
        $this->assertTrue($definition->hasOption('backoffice-display-namespace'));
    }
}
