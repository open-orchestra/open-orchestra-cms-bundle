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

    /**
     * Set up the test
     */
    public function setUp()
    {
        $status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');//+

        $this->eventDispatcher = Phake::mock('Symfony\Component\EventDispatcher\EventDispatcherInterface');//+
        $this->statusRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface');//+
        Phake::when($this->statusRepository)->find(Phake::anyParameters())->thenReturn($status);//+

        $facade = Phake::mock('OpenOrchestra\BaseApi\Facade\FacadeInterface');
        $facade->label = 'status';
        $facade->name = 'name';
        $transformer = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerInterface');
        Phake::when($transformer)->transform(Phake::anyParameters())->thenReturn($facade);

        $router = Phake::mock('Symfony\Component\Routing\RouterInterface');

        $this->transformerManager = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerManager');
        Phake::when($this->transformerManager)->get(Phake::anyParameters())->thenReturn($transformer);
        Phake::when($this->transformerManager)->getRouter()->thenReturn($router);

        $this->contentTransformer = new ContentTransformer($this->statusRepository, $this->eventDispatcher);//+
        $this->contentTransformer->setContext($this->transformerManager);//+
    }

    /**
     * test transform
     */
    public function testTransform()
    {
        $content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content)->getId()->thenReturn(1);
        $contentData = 'contentData';
        $contentDate = '2015/09/01';
        $contentBool = true;
        $attribute = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentAttributeInterface');
        Phake::when($content)->getAttributes()->thenReturn(array($attribute, $attribute));
        Phake::when($content)->getContentId()->thenReturn($contentData);
        Phake::when($content)->getContentType()->thenReturn($contentData);
        Phake::when($content)->getName()->thenReturn($contentData);
        Phake::when($content)->getVersion()->thenReturn($contentData);
        Phake::when($content)->getContentTypeVersion()->thenReturn($contentData);
        Phake::when($content)->getLanguage()->thenReturn($contentData);
        Phake::when($content)->isDeleted()->thenReturn($contentBool);
        Phake::when($content)->isLinkedToSite()->thenReturn($contentBool);
        Phake::when($content)->getCreatedAt()->thenReturn($contentDate);
        Phake::when($content)->getUpdatedAt()->thenReturn($contentDate);

        $facade = $this->contentTransformer->transform($content);

        $this->assertSame($contentData, $facade->id);
        $this->assertSame($contentData, $facade->contentType);
        $this->assertSame($contentData, $facade->name);
        $this->assertSame($contentData, $facade->version);
        $this->assertSame($contentData, $facade->contentTypeVersion);
        $this->assertSame($contentData, $facade->language);
        $this->assertSame($facade->status->label, $facade->statusLabel);
        $this->assertSame($contentDate, $facade->createdAt);
        $this->assertSame($contentDate, $facade->updatedAt);
        $this->assertSame($contentBool, $facade->deleted);
        $this->assertSame($contentBool, $facade->linkedToSite);
        Phake::verify($content, Phake::times(1))->getStatus();

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
     * Test Exception transform with wrong object a parameters
     */
    public function testExceptionTransform()
    {
        $this->setExpectedException('OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException');
        $this->contentTransformer->transform(Phake::mock('stdClass'));
    }

    /**
     * test reverseTransform
     *
     * @param FacadeInterface $facade
     * @param ContentInterface $source
     * @param int $searchCount
     * @param int $setCount
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

        $eventDispatcher = clone $this->eventDispatcher;
        $facade->statusId = 'statusId';

        Phake::when($eventDispatcher)->dispatch(Phake::anyParameters())->thenThrow(Phake::mock('OpenOrchestra\Backoffice\Exception\StatusChangeNotGrantedException'));

        $contentTransformer = new ContentTransformer($this->statusRepository, $eventDispatcher);//+
        $contentTransformer->setContext($this->transformerManager);

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
