<?php

namespace PHPOrchestra\BackofficeBundle\Test\Manager;

use PHPOrchestra\BackofficeBundle\Manager\ContentManager;
use Phake;
use PHPOrchestra\ModelInterface\Model\ContentInterface;

/**
 * Class ContentManagerTest
 */
class ContentManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContentManager
     */
    protected $manager;

    protected $contentRepository;
    protected $contextManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->contentRepository = Phake::mock('PHPOrchestra\ModelInterface\Repository\ContentRepositoryInterface');

        $this->contextManager = Phake::mock('PHPOrchestra\Backoffice\Context\ContextManager');
        Phake::when($this->contextManager)->getCurrentLocale()->thenReturn('fakeLanguage');

        $this->manager = new ContentManager($this->contentRepository, $this->contextManager);
    }

    /**
     * @param ContentInterface|null $contentFindOneBy
     * @param ContentInterface|null $contentFindOneByContentId
     * @param ContentInterface      $expectedContent
     *
     * @dataProvider provideContent
     */
    public function testCreateNewLanguageContent($contentFindOneBy, $contentFindOneByContentId, ContentInterface $expectedContent)
    {

        Phake::when($this->contentRepository)->findOneByContentIdAndLanguage(Phake::anyParameters())->thenReturn($contentFindOneBy);
        Phake::when($this->contentRepository)->findOneByContentId(Phake::anyParameters())->thenReturn($contentFindOneByContentId);

        $content = $this->manager->createNewLanguageContent('fakeId', null);

        $this->assertEquals($expectedContent, $content);

    }

    /**
     * @param ContentInterface   $content
     * @param int                $expectedVersion
     *
     * @dataProvider provideDuplicateContent
     */
    public function testDuplicateNode(ContentInterface $content, $expectedVersion)
    {
        $newContent = $this->manager->duplicateContent($content);

        Phake::verify($newContent)->setVersion($expectedVersion);
        Phake::verify($newContent)->setStatus(null);
    }

    /**
     * @return array
     */
    public function provideContent()
    {
        $content0 = Phake::mock('PHPOrchestra\ModelInterface\Model\ContentInterface');

        $content1 = Phake::mock('PHPOrchestra\ModelInterface\Model\ContentInterface');

        return array(
            array($content0, null, $content0),
            array(null, $content1, $content1)
        );
    }

    /**
     * @return array
     */
    public function provideDuplicateContent()
    {
        $content0 = Phake::mock('PHPOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content0)->getVersion()->thenReturn(0);

        $content1 = Phake::mock('PHPOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content1)->getVersion()->thenReturn(1);

        $content2 = Phake::mock('PHPOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content2)->getVersion()->thenReturn(null);

        return array(
            array($content0, 1),
            array($content1, 2),
            array($content2, 1),
        );
    }

}
