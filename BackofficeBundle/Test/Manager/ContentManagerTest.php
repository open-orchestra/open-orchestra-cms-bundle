<?php

namespace PHPOrchestra\BackofficeBundle\Test\Manager;

use PHPOrchestra\BackofficeBundle\Manager\ContentManager;
use PHPOrchestra\ModelBundle\Document\Content;
use PHPOrchestra\ModelBundle\Model\ContentInterface;
use PHPOrchestra\ModelBundle\Repository\ContentRepository;
use Phake;
use Doctrine\Common\Collections\ArrayCollection;

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
        $this->contentRepository = Phake::mock('PHPOrchestra\ModelBundle\Repository\ContentRepository');

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

        Phake::when($this->contentRepository)->findOneBy(Phake::anyParameters())->thenReturn($contentFindOneBy);
        Phake::when($this->contentRepository)->findOneByContentId(Phake::anyParameters())->thenReturn($contentFindOneByContentId);

        $content = $this->manager->createNewLanguageContent('fakeId', null);

        $this->assertEquals($expectedContent, $content);

    }

    /**
     * @return array
     */
    public function provideContent()
    {
        $content0 = Phake::mock('PHPOrchestra\ModelBundle\Model\ContentInterface');

        $content1 = Phake::mock('PHPOrchestra\ModelBundle\Model\ContentInterface');

        return array(
            array($content0, null, $content0),
            array(null, $content1, $content1)
        );
    }
}
