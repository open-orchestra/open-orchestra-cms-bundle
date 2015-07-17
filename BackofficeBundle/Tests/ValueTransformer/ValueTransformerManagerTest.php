<?php

namespace OpenOrchestra\BackofficeBundle\Tests\ValueTransformer;

use OpenOrchestra\Backoffice\ValueTransformer\ValueTransformerManager;
use Phake;

/**
 * Class ValueTransformerManagerTest
 */
class ValueTransformerManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $manager;
    protected $valueTransformer;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->manager = new ValueTransformerManager();
        $this->valueTransformer = Phake::mock('OpenOrchestra\Backoffice\ValueTransformer\ValueTransformerInterface');
        Phake::when($this->valueTransformer)->getName()->thenReturn('foo');
        Phake::when($this->valueTransformer)->transform(Phake::anyParameters())->thenReturn('foo');
        Phake::when($this->valueTransformer)->support(Phake::anyParameters())->thenReturn(true);
    }

    public function testGetter()
    {
        $value = $this->manager->transform('foo','bar');
        $this->assertEquals($value, 'bar');

        $this->manager->addStrategy($this->valueTransformer);
        $value = $this->manager->transform('foo','bar');
        $this->assertEquals($value, 'foo');

        Phake::verify($this->valueTransformer)->support('foo','bar');
        Phake::verify($this->valueTransformer)->transform('bar');
    }
}
