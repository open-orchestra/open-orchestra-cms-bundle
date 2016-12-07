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
    protected $contentTypeRepository;
    protected $eventDispatcher;
    protected $authorizationChecker;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->eventDispatcher = Phake::mock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $contentType0 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        Phake::when($contentType0)->isDefiningVersionable()->thenReturn(true);
        $contentType1 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        Phake::when($contentType1)->isDefiningVersionable()->thenReturn(false);

        $this->contentTypeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface');
        Phake::when($this->contentTypeRepository)->findOneByContentTypeIdInLastVersion('bar')->thenReturn($contentType0);
        Phake::when($this->contentTypeRepository)->findOneByContentTypeIdInLastVersion('baz')->thenReturn($contentType1);

        $status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        $this->statusRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface');
        Phake::when($this->statusRepository)->find(Phake::anyParameters())->thenReturn($status);

        $router = Phake::mock('Symfony\Component\Routing\RouterInterface');

        $facade = Phake::mock('OpenOrchestra\BaseApi\Facade\FacadeInterface');
        $facade->label = 'status';
        $facade->name = 'name';
        $transformer = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerInterface');
        Phake::when($transformer)->transform(Phake::anyParameters())->thenReturn($facade);
        $this->transformerManager = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerManager');
        Phake::when($this->transformerManager)->get(Phake::anyParameters())->thenReturn($transformer);
        Phake::when($this->transformerManager)->getRouter()->thenReturn($router);

        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        Phake::when($this->authorizationChecker)->isGranted(Phake::anyParameters())->thenReturn(true);

        $this->contentTransformer = new ContentTransformer($this->facadeClass, $this->statusRepository, $this->contentTypeRepository, $this->eventDispatcher, $this->authorizationChecker);
        $this->contentTransformer->setContext($this->transformerManager);
    }

    /**
     * @param string   $contentId
     * @param string   $contentType
     * @param string   $name
     * @param int      $version
     * @param int      $contentTypeVersion
     * @param string   $language
     * @param DateTime $creationDate
     * @param DateTime $updateDate
     * @param bool     $deleted
     * @param bool     $linkedToSite
     * @param bool     $getVersion
     *
     * @dataProvider provideContentData
     */
    public function testTransform(
        $contentId,
        $contentType,
        $name,
        $version,
        $contentTypeVersion,
        $language,
        $creationDate,
        $updateDate,
        $deleted,
        $linkedToSite,
        $getVersion
    ) {
        $attribute = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentAttributeInterface');
        $content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content)->getAttributes()->thenReturn(array($attribute, $attribute));
        Phake::when($content)->getContentId()->thenReturn($contentId);
        Phake::when($content)->getContentType()->thenReturn($contentType);
        Phake::when($content)->getName()->thenReturn($name);
        Phake::when($content)->getVersion()->thenReturn($version);
        Phake::when($content)->getContentTypeVersion()->thenReturn($contentTypeVersion);
        Phake::when($content)->getLanguage()->thenReturn($language);
        Phake::when($content)->getCreatedAt()->thenReturn($creationDate);
        Phake::when($content)->getUpdatedAt()->thenReturn($updateDate);
        Phake::when($content)->isDeleted()->thenReturn($deleted);
        Phake::when($content)->isLinkedToSite()->thenReturn($linkedToSite);
        $status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status)->isBlocked()->thenReturn(false);
        Phake::when($content)->getStatus()->thenReturn($status);

        $facade = $this->contentTransformer->transform($content);

        $this->assertSame($contentId, $facade->id);
        $this->assertSame($contentType, $facade->contentType);
        $this->assertSame($name, $facade->name);
        $this->assertSame($version, $facade->version);
        $this->assertSame($contentTypeVersion, $facade->contentTypeVersion);
        $this->assertSame($language, $facade->language);
        $this->assertSame($creationDate, $facade->createdAt);
        $this->assertSame($updateDate, $facade->updatedAt);
        $this->assertSame($deleted, $facade->deleted);
        $this->assertSame($linkedToSite, $facade->linkedToSite);
        $this->assertSame($facade->status->label, $facade->statusLabel);
        Phake::verify($content, Phake::times(2))->getStatus();

        $this->assertInstanceOf('OpenOrchestra\ApiBundle\Facade\ContentFacade', $facade);
        $this->assertArrayHasKey('_self_form', $facade->getLinks());
        $this->assertSame($getVersion, array_key_exists('_self_new_version', $facade->getLinks()));
        $this->assertSame($getVersion, array_key_exists('_self_version', $facade->getLinks()));
        $this->assertArrayHasKey('_language_list', $facade->getLinks());
        $this->assertArrayHasKey('_self_without_parameters', $facade->getLinks());
        $this->assertArrayHasKey('_self_delete', $facade->getLinks());
        $this->assertArrayHasKey('_status_list', $facade->getLinks());
        $this->assertArrayHasKey('_self_status_change', $facade->getLinks());
    }

    /**
     * @return array
     */
    public function provideContentData()
    {
        $date1 = new DateTime();
        $date2 = new DateTime();

        return array(
            array('foo', 'bar', 'baz', 1, 2, 'fr', $date1, $date2, true, false, true),
            array('bar', 'baz', 'foo', 2, 1, 'en', $date2, $date1, false, true, false),
        );
    }

    /**
     * Test Exception transform with wrong object a parameters
     */
    public function testExceptionTransform()
    {
        $this->setExpectedException('OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException');
        $this->contentTransformer->transform(Phake::mock('stdClass'));
    }

    /**
     * test reverseTransform
     *
     * @param FacadeInterface  $facade
     * @param ContentInterface $source
     * @param int              $searchCount
     * @param int              $setCount
     *
     * @dataProvider changeStatusProvider
     */
    public function testReverseTransform($facade, $source, $searchCount, $setCount)
    {
        $this->contentTransformer->reverseTransform($facade, $source);

        Phake::verify($this->statusRepository, Phake::times($searchCount))->find(Phake::anyParameters());
        Phake::verify($this->eventDispatcher, Phake::times($setCount))->dispatch(Phake::anyParameters());
    }

    /**
     * Test Exception reverse transform with wrong object a parameters
     */
    public function testExceptionReverseTransform()
    {
        $facade = Phake::mock('OpenOrchestra\ApiBundle\Facade\ContentFacade');
        $source = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');

        $facade->statusId = 'statusId';

        Phake::when($this->eventDispatcher)->dispatch(Phake::anyParameters())->thenThrow(Phake::mock('OpenOrchestra\Backoffice\Exception\StatusChangeNotGrantedException'));

        $this->setExpectedException('OpenOrchestra\ApiBundle\Exceptions\HttpException\StatusChangeNotGrantedHttpException');
        $this->contentTransformer->reverseTransform($facade, $source);
    }

    /**
     * @return array
     */
    public function changeStatusProvider()
    {
        $content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');

        $fromStatus = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($fromStatus)->getId()->thenReturn('fromStatus');
        Phake::when($content)->getStatus()->thenReturn($fromStatus);

        $facade1 = Phake::mock('OpenOrchestra\ApiBundle\Facade\ContentFacade');

        $facade2 = Phake::mock('OpenOrchestra\ApiBundle\Facade\ContentFacade');
        $facade2->statusId = 'statusId';

        return array(
            array($facade1, null, 0, 0),
            array($facade1, $content, 0, 0),
            array($facade2, $content, 1, 1)
        );
    }

    /**
     * Test getName
     */
    public function testGetName()
    {
        $this->assertSame('content', $this->contentTransformer->getName());
    }
}
