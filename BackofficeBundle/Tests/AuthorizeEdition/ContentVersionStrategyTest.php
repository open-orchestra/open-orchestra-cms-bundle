<?php

namespace OpenOrchestra\BackofficeBundle\Tests\AuthorizeEdition;

use OpenOrchestra\Backoffice\AuthorizeEdition\ContentVersionStrategy;
use Phake;

/**
 * Test ContentVersionStrategyTest
 */
class ContentVersionStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContentVersionStrategy
     */
    protected $strategy;

    protected $content;
    protected $contentRepository;

    /**
     * Set up the test
     */
    protected function setUp()
    {
        $this->content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        $this->contentRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface');
        Phake::when($this->contentRepository)->findOneByLanguage(Phake::anyParameters())->thenReturn($this->content);

        $this->strategy = new ContentVersionStrategy($this->contentRepository);

    }

    /**
     * Test the instance
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

        $this->assertSame($support, $this->strategy->support($document));
    }

    /**
     * @return array
     */
    public function provideDocumentAndSupport()
    {
        return array(
            array('OpenOrchestra\ModelInterface\Model\StatusableInterface', false),
            array('OpenOrchestra\ModelInterface\Model\ContentInterface', true),
            array('OpenOrchestra\ModelInterface\Model\NodeInterface', false),
            array('stdClass', false),
        );
    }

    /**
     * @param $presentVersion
     * @param $otherVersion
     * @param $editable
     *
     * @dataProvider provideVersionAndEditable
     */
    public function testIsEditable($presentVersion, $otherVersion, $editable)
    {
        $content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content)->getVersion()->thenReturn($presentVersion);
        Phake::when($this->content)->getVersion()->thenReturn($otherVersion);

        $this->assertSame($editable, $this->strategy->isEditable($content));
    }

    /**
     * @return array
     */
    public function provideVersionAndEditable()
    {
        return array(
            array(1, 1, true),
            array(2, 1, true),
            array(1, 2, false),
        );
    }

    public function testIsEditableWithNoContent()
    {
        Phake::when($this->contentRepository)->findOneByLanguage(Phake::anyParameters())->thenReturn(null);
        $content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');

        $this->assertTrue($this->strategy->isEditable($content));
    }
}
