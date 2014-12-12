<?php

namespace PHPOrchestra\ApiBundle\Test\Transformer;

use Phake;
use PHPOrchestra\ApiBundle\Transformer\ContentTransformer;

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
    protected $transformer;
    protected $statusId;
    protected $content;
    protected $router;
    protected $status;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->content = Phake::mock('PHPOrchestra\ModelBundle\Model\ContentInterface');
        Phake::when($this->content)->getAttributes()->thenReturn(array());
        $this->status = Phake::mock('PHPOrchestra\ModelBundle\Document\Status');
        $this->statusId = 'StatusId';
        Phake::when($this->status)->getId(Phake::anyParameters())->thenReturn($this->statusId);

        $this->statusRepository = Phake::mock('PHPOrchestra\ModelBundle\Repository\StatusRepository');
        Phake::when($this->statusRepository)->find(Phake::anyParameters())->thenReturn($this->status);

        $this->transformer = Phake::mock('PHPOrchestra\ApiBundle\Transformer\StatusTransformer');
        $this->router = Phake::mock('Symfony\Component\Routing\RouterInterface');
        Phake::when($this->router)->generate(Phake::anyParameters())->thenReturn('route');

        $this->transformerManager = Phake::mock('PHPOrchestra\ApiBundle\Transformer\TransformerManager');
        Phake::when($this->transformerManager)->get(Phake::anyParameters())->thenReturn($this->transformer);
        Phake::when($this->transformerManager)->getRouter()->thenReturn($this->router);

        $this->contentTransformer = new ContentTransformer($this->statusRepository);
        $this->contentTransformer->setContext($this->transformerManager);
    }

    /**
     * test transform
     */
    public function testTransform()
    {
        $facade = Phake::mock('PHPOrchestra\ApiBundle\Facade\FacadeInterface');
        $facade->label = 'draft';
        Phake::when($this->transformer)->transform(Phake::anyParameters())->thenReturn($facade);

        $facade = $this->contentTransformer->transform($this->content);

        $this->assertInstanceOf('PHPOrchestra\ApiBundle\Facade\ContentFacade', $facade);
        $this->assertArrayHasKey('_self_form', $facade->getLinks());
        $this->assertArrayHasKey('_self', $facade->getLinks());
        $this->assertArrayHasKey('_self_delete', $facade->getLinks());
        $this->assertArrayHasKey('_status_list', $facade->getLinks());
        $this->assertArrayHasKey('_self_status_change', $facade->getLinks());
    }

    /**
     * test reverseTransform
     *
     * @dataProvider getChangeStatus
     */
    public function testReverseTransform($facade, $source, $searchCount, $setCount)
    {
        $this->contentTransformer->reverseTransform($facade, $source);

        Phake::verify($this->statusRepository, Phake::times($searchCount))->find(Phake::anyParameters());

        if ($source) {
            Phake::verify($source, Phake::times($setCount))->setStatus(Phake::anyParameters());
        }
    }

    /**
     * @return array
     */
    public function getChangeStatus()
    {
        $facadeA = Phake::mock('PHPOrchestra\ApiBundle\Facade\ContentFacade');

        $facadeB = Phake::mock('PHPOrchestra\ApiBundle\Facade\ContentFacade');
        $facadeB->statusId = 'fakeId';

        $content = Phake::mock('PHPOrchestra\ModelBundle\Model\ContentInterface');

        return array(
            array($facadeA, null, 0, 0),
            array($facadeA, $content, 0, 0),
            array($facadeB, $content, 1, 1)
        );
    }
}
