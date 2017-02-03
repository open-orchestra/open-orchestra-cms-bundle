<?php

namespace OpenOrchestra\ApiBundle\Tests\Transformer;

use DateTime;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\ApiBundle\Transformer\ContentTransformer;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;

/**
 * Class ContentTransformerTest
 */
class ContentTransformerTest extends AbstractBaseTestCase
{
    /**
     * @var ContentTransformer
     */
    protected $contentTransformer;

    protected $facadeClass = 'OpenOrchestra\ApiBundle\Facade\ContentFacade';
    protected $transformerManager;
    protected $statusRepository;
    protected $contentRepository;
    protected $authorizationChecker;
    protected $contextManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $contentType0 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        Phake::when($contentType0)->isDefiningVersionable()->thenReturn(true);
        $contentType1 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        Phake::when($contentType1)->isDefiningVersionable()->thenReturn(false);

        $status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        $this->statusRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface');
        Phake::when($this->statusRepository)->find(Phake::anyParameters())->thenReturn($status);

        $router = Phake::mock('Symfony\Component\Routing\RouterInterface');

        $facade = Phake::mock('OpenOrchestra\BaseApi\Facade\FacadeInterface');
        $facade->label = 'fakeStatus';
        $facade->name = 'name';
        $transformer = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerInterface');
        Phake::when($transformer)->transform(Phake::anyParameters())->thenReturn($facade);
        $this->transformerManager = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerManager');
        Phake::when($this->transformerManager)->get(Phake::anyParameters())->thenReturn($transformer);
        Phake::when($this->transformerManager)->getRouter()->thenReturn($router);
        $groupContext = Phake::mock('OpenOrchestra\BaseApi\Context\GroupContext');
        Phake::when($groupContext)->hasGroup(Phake::anyParameters())->thenReturn(true);
        Phake::when($this->transformerManager)->getGroupContext()->thenReturn($groupContext);

        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        Phake::when($this->authorizationChecker)->isGranted(Phake::anyParameters())->thenReturn(true);

        $this->contentRepository = Phake::mock('OpenOrchestra\ModelBundle\Repository\ContentRepository');
        Phake::when($this->contentRepository)->find(Phake::anyParameters())->thenReturn(Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface'));
        Phake::when($this->contentRepository)->findAllPublishedByContentId(Phake::anyParameters())->thenReturn(array());
        Phake::when($this->contentRepository)->findOneByContentId(Phake::anyParameters())->thenReturn(Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface'));

        $this->contextManager = Phake::mock('OpenOrchestra\Backoffice\Context\ContextManager');
        Phake::when($this->contextManager)->getCurrentLocale()->thenReturn('en');

        $this->contentTransformer = new ContentTransformer(
            $this->facadeClass,
            $this->statusRepository,
            $this->contentRepository,
            $this->authorizationChecker,
            $this->contextManager
        );
        $this->contentTransformer->setContext($this->transformerManager);
    }

    /**
     * @param string   $id
     * @param string   $contentId
     * @param string   $contentType
     * @param string   $name
     * @param int      $version
     * @param string   $language
     * @param DateTime $creationDate
     * @param DateTime $updateDate
     * @param bool     $deleted
     * @param bool     $linkedToSite
     *
     * @dataProvider provideContentData
     */
    public function testTransform(
        $id,
        $contentId,
        $contentType,
        $name,
        $version,
        $language,
        $creationDate,
        $updateDate,
        $deleted,
        $linkedToSite
    ) {
        $attribute = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentAttributeInterface');
        $content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content)->getAttributes()->thenReturn(array($attribute, $attribute));
        Phake::when($content)->getId()->thenReturn($id);
        Phake::when($content)->getContentId()->thenReturn($contentId);
        Phake::when($content)->getContentType()->thenReturn($contentType);
        Phake::when($content)->getName()->thenReturn($name);
        Phake::when($content)->getVersion()->thenReturn($version);
        Phake::when($content)->getLanguage()->thenReturn($language);
        Phake::when($content)->getCreatedAt()->thenReturn($creationDate);
        Phake::when($content)->getUpdatedAt()->thenReturn($updateDate);
        Phake::when($content)->isDeleted()->thenReturn($deleted);
        Phake::when($content)->isLinkedToSite()->thenReturn($linkedToSite);
        $status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status)->isBlocked()->thenReturn(false);
        Phake::when($status)->getLabel(Phake::anyParameters())->thenReturn('fakeStatus');
        Phake::when($content)->getStatus()->thenReturn($status);

        $facade = $this->contentTransformer->transform($content);

        $this->assertSame($id, $facade->id);
        $this->assertSame($contentId, $facade->contentId);
        $this->assertSame($name, $facade->name);
        $this->assertSame($version, $facade->version);
        $this->assertSame($language, $facade->language);
        $this->assertSame($creationDate, $facade->createdAt);
        $this->assertSame($updateDate, $facade->updatedAt);
        $this->assertSame($deleted, $facade->deleted);
        $this->assertSame($linkedToSite, $facade->linkedToSite);
        $this->assertSame($facade->status->label, $facade->statusLabel);

        Phake::verify($content, Phake::times(3))->getStatus();

        $this->assertInstanceOf('OpenOrchestra\ApiBundle\Facade\ContentFacade', $facade);

        $this->assertArrayHasKey('can_delete', $facade->getRights());
    }

    /**
     * @return array
     */
    public function provideContentData()
    {
        $date1 = new DateTime();
        $date2 = new DateTime();

        return array(
            array('foo', 'content_id', 'bar', 'baz', 1, 'fr', $date1, $date2, true, false),
            array('bar', 'content_id2', 'baz', 'foo', 2, 'en', $date2, $date1, false, true),
        );
    }

    /**
     * Test Exception transform with wrong object a parameters
     */
    public function testExceptionTransform()
    {
        $this->expectException('OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException');
        $this->contentTransformer->transform(Phake::mock('stdClass'));
    }

    /**
     * test reverseTransform
     */
    public function testReverseTransform()
    {
        $facade = Phake::mock('OpenOrchestra\ApiBundle\Facade\ContentFacade');
        $facade->id = 'fakeId';

        $result = $this->contentTransformer->reverseTransform($facade);

        Phake::verify($this->contentRepository)->findById('fakeId');
        $this->assertNull($result);
    }

    /**
     * Test getName
     */
    public function testGetName()
    {
        $this->assertSame('content', $this->contentTransformer->getName());
    }
}
