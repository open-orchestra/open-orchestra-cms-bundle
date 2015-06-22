<?php

namespace OpenOrchestra\MediaAdminBundle\Tests\ExtractReference;

use OpenOrchestra\MediaAdminBundle\ExtractReference\ExtractReferenceManager;
use Phake;

/**
 * Test ExtractReferenceManagerTest
 */
class ExtractReferenceManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExtractReferenceManager
     */
    protected $manager;

    protected $strategy;
    protected $element;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->element = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusableInterface');
        $this->strategy = Phake::mock('OpenOrchestra\MediaAdminBundle\ExtractReference\ExtractReferenceInterface');

        $this->manager = new ExtractReferenceManager();
        $this->manager->addStrategy($this->strategy);
    }

    /**
     * Test when strategy supports
     */
    public function testIfStrategySupports()
    {
        Phake::when($this->strategy)->support(Phake::anyParameters())->thenReturn(true);

        $this->manager->extractReference($this->element);

        Phake::verify($this->strategy)->extractReference($this->element);
    }
    
    /**
     * Test when strategy does not supports
     */
    public function testStrategyDoesNotSupports()
    {
        Phake::when($this->strategy)->support(Phake::anyParameters())->thenReturn(false);

        $this->setExpectedException('OpenOrchestra\MediaAdminBundle\Exceptions\ExtractReferenceStrategyNotFound');

        $this->manager->extractReference($this->element);

        Phake::verify($this->strategy, Phake::never())->extractReference(Phake::anyParameters());
    }
}
