<?php

namespace OpenOrchestra\Backoffice\Tests\ValueTransformer;

use OpenOrchestra\Backoffice\ValueTransformer\ValueTransformerManager;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class ValueTransformerManagerTest
 */
class ValueTransformerManagerTest extends AbstractBaseTestCase
{
    /**
     * @var ValueTransformerManager
     */
    protected $manager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->manager = new ValueTransformerManager();
    }

    /**
     * Test transformation
     */
    public function testTransform()
    {
        $value = $this->manager->transform('foo','bar');
        $this->assertEquals($value, 'bar');

        $valueTransformer = Phake::mock('OpenOrchestra\Backoffice\ValueTransformer\ValueTransformerInterface');
        Phake::when($valueTransformer)->getName()->thenReturn('foo');
        Phake::when($valueTransformer)->transform(Phake::anyParameters())->thenReturn('foo');
        Phake::when($valueTransformer)->support(Phake::anyParameters())->thenReturn(true);

        $this->manager->addStrategy($valueTransformer);
        $value = $this->manager->transform('foo','bar');
        $this->assertEquals($value, 'foo');

        Phake::verify($valueTransformer)->support('foo','bar');
        Phake::verify($valueTransformer)->transform('bar');
    }

    /**
     * @param mixed $value
     *
     * @dataProvider provideNoString
     */
    public function testTransformWhenNotDone($value)
    {
        $this->setExpectedException('OpenOrchestra\Backoffice\Exception\ValueTransfomationFailedException');
        $this->manager->transform('foo', $value);
    }

    /**
     * @return array
     */
    public function provideNoString()
    {
        return array(
            array(1),
            array(Phake::mock('stdClass')),
            array(array()),
        );
    }
}
