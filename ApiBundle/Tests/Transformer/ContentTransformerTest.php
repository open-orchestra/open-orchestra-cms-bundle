<?php

namespace OpenOrchestra\ApiBundle\Tests\Transformer;

use Phake;
use OpenOrchestra\ApiBundle\Transformer\ContentTransformer;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
/**
 * Class ContentTransformerTest
 */
class ContentTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContentTransformer
     */
    protected $contentTransformer;

    protected $transformerManager;
    protected $statusRepository;
    protected $eventDispatcher;
    protected $transformer;
    protected $contentData;
    protected $contentDate;
    protected $contentBool;
    protected $statusId;
    protected $content;
    protected $facade;
    protected $router;
    protected $status;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($this->content)->getId()->thenReturn(1);
        $this->status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        $this->contentData = 'contentData';
        $this->contentDate = '2015/09/01';
        $this->contentBool = true;
        Phake::when($this->content)->getContentId(Phake::anyParameters())->thenReturn($this->contentData);
        Phake::when($this->content)->getContentType(Phake::anyParameters())->thenReturn($this->contentData);
        Phake::when($this->content)->getName(Phake::anyParameters())->thenReturn($this->contentData);
        Phake::when($this->content)->getVersion(Phake::anyParameters())->thenReturn($this->contentData);
        Phake::when($this->content)->getContentTypeVersion(Phake::anyParameters())->thenReturn($this->contentData);
        Phake::when($this->content)->getLanguage(Phake::anyParameters())->thenReturn($this->contentData);
        Phake::when($this->content)->isDeleted(Phake::anyParameters())->thenReturn($this->contentBool);
        Phake::when($this->content)->isLinkedToSite(Phake::anyParameters())->thenReturn($this->contentBool);
        Phake::when($this->content)->getCreatedAt(Phake::anyParameters())->thenReturn($this->contentDate);
        Phake::when($this->content)->getUpdatedAt(Phake::anyParameters())->thenReturn($this->contentDate);

        $this->eventDispatcher = Phake::mock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->statusRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface');
        Phake::when($this->statusRepository)->find(Phake::anyParameters())->thenReturn($this->status);

        $this->facade = Phake::mock('OpenOrchestra\BaseApi\Facade\FacadeInterface');
        $this->facade->label = 'status';
        $this->facade->name = 'name';
        $this->transformer = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerInterface');
        Phake::when($this->transformer)->transform(Phake::anyParameters())->thenReturn($this->facade);

        $this->router = Phake::mock('Symfony\Component\Routing\RouterInterface');
        Phake::when($this->router)->generate(Phake::anyParameters())->thenReturn('route');

        $this->transformerManager = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerManager');
        Phake::when($this->transformerManager)->get(Phake::anyParameters())->thenReturn($this->transformer);
        Phake::when($this->transformerManager)->getRouter()->thenReturn($this->router);

        $this->contentTransformer = new ContentTransformer($this->statusRepository, $this->eventDispatcher);
        $this->contentTransformer->setContext($this->transformerManager);
    }

    /**
     * test transform
     */
    public function testTransform()
    {
        $attribute = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentAttributeInterface');
        Phake::when($this->content)->getAttributes()->thenReturn(array($attribute, $attribute));

        $facade = $this->contentTransformer->transform($this->content);

        $this->assertSame($this->contentData,$facade->id);
        $this->assertSame($this->contentData,$facade->contentType);
        $this->assertSame($this->contentData,$facade->name);
        $this->assertSame($this->contentData,$facade->version);
        $this->assertSame($this->contentData,$facade->contentTypeVersion);
        $this->assertSame($this->contentData,$facade->language);
        $this->assertSame($facade->status->label,$facade->statusLabel);
        $this->assertSame($this->contentDate,$facade->createdAt);
        $this->assertSame($this->contentDate,$facade->updatedAt);
        $this->assertSame($this->contentBool,$facade->deleted);
        $this->assertSame($this->contentBool,$facade->linkedToSite);
        Phake::verify($this->content, Phake::times(1))->getStatus();

        $this->assertInstanceOf('OpenOrchestra\ApiBundle\Facade\ContentFacade', $facade);
        $this->assertArrayHasKey('_self_form', $facade->getLinks());
        $this->assertArrayHasKey('_self_duplicate', $facade->getLinks());
        $this->assertArrayHasKey('_self_version', $facade->getLinks());
        $this->assertArrayHasKey('_language_list', $facade->getLinks());
        $this->assertArrayHasKey('_self', $facade->getLinks());
        $this->assertArrayHasKey('_self_without_parameters', $facade->getLinks());
        $this->assertArrayHasKey('_self_delete', $facade->getLinks());
        $this->assertArrayHasKey('_status_list', $facade->getLinks());
        $this->assertArrayHasKey('_self_status_change', $facade->getLinks());
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
