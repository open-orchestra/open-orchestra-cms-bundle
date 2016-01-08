<?php

namespace OpenOrchestra\Backoffice\Tests\AuthorizeEdition;

use OpenOrchestra\Backoffice\AuthorizeEdition\StatusableStrategy;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Test StatusableStrategyTest
 */
class StatusableStrategyTest extends AbstractBaseTestCase
{
    /**
     * @var StatusableStrategy
     */
    protected $strategy;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->strategy = new StatusableStrategy();
    }

    /**
     * Test implementation
     */
    public function testInstance()
    {
        $this->assertInstanceOf('OpenOrchestra\Backoffice\AuthorizeEdition\AuthorizeEditionInterface', $this->strategy);
    }

    /**
     * @param string $document
     * @param bool   $support
     *
     * @dataProvider provideDocumentAndSupport
     */
    public function testSupport($document, $support)
    {
        $document = Phake::mock($document);
        Phake::when($document)->getStatus()->thenReturn(Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface'));

        $this->assertSame($support, $this->strategy->support($document));
    }

    /**
     * @return array
     */
    public function provideDocumentAndSupport()
    {
        return array(
            array('OpenOrchestra\ModelInterface\Model\StatusableInterface', true),
            array('OpenOrchestra\ModelInterface\Model\NodeInterface', true),
            array('stdClass', false),
        );
    }

    /**
     * @param bool $published
     * @param bool $editable
     *
     * @dataProvider providePublishedAndEditable
     */
    public function testIsEditable($published, $editable)
    {
        $status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status)->isPublished()->thenReturn($published);
        $document = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusableInterface');
        Phake::when($document)->getStatus()->thenReturn($status);

        $this->assertSame($editable, $this->strategy->isEditable($document));
    }

    /**
     * @return array
     */
    public function providePublishedAndEditable()
    {
        return array(
            array(true, false),
            array(false, true),
        );
    }
}
