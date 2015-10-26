<?php

namespace OpenOrchestra\ApiBundle\Tests\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use Phake;
use OpenOrchestra\ApiBundle\Transformer\NodeTransformer;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class NodeTransformerTest
 */
class NodeTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodeTransformer
     */
    protected $nodeTransformer;

    protected $authorizeEditionManager;
    protected $authorizationChecker;
    protected $roleName = 'ROLE_NAME';
    protected $transformerManager;
    protected $encryptionManager;
    protected $statusRepository;
    protected $eventDispatcher;
    protected $siteRepository;
    protected $transformer;
    protected $statusId;
    protected $router;
    protected $status;
    protected $node;
    protected $site;
    protected $role;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->authorizeEditionManager = Phake::mock('OpenOrchestra\BackofficeBundle\StrategyManager\AuthorizeEditionManager');

        $this->eventDispatcher = Phake::mock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $this->node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($this->node)->getStatus()->thenReturn($status);
        Phake::when($status)->isPublished()->thenReturn(false);

        $siteAlias = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteAliasInterface');
        $this->site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($this->site)->getAliases()->thenReturn(array($siteAlias));

        $this->role = Phake::mock('OpenOrchestra\ModelInterface\Model\RoleInterface');
        Phake::when($this->role)->getName()->thenReturn($this->roleName);

        $this->status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        $this->statusId = 'StatusId';
        Phake::when($this->status)->getId(Phake::anyParameters())->thenReturn($this->statusId);
        Phake::when($this->status)->getToRoles()->thenReturn(new ArrayCollection(array($this->role)));

        $this->encryptionManager = Phake::mock('OpenOrchestra\BaseBundle\Manager\EncryptionManager');

        $this->siteRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface');
        Phake::when($this->siteRepository)->findOneBySiteId(Phake::anyParameters())->thenReturn($this->site);

        $this->statusRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface');
        Phake::when($this->statusRepository)->find(Phake::anyParameters())->thenReturn($this->status);

        $this->transformer = Phake::mock('OpenOrchestra\ApiBundle\Transformer\BlockTransformer');
        $this->router = Phake::mock('Symfony\Component\Routing\RouterInterface');
        Phake::when($this->router)->generate(Phake::anyParameters())->thenReturn('route');

        $this->transformerManager = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerManager');
        Phake::when($this->transformerManager)->get(Phake::anyParameters())->thenReturn($this->transformer);
        Phake::when($this->transformerManager)->getRouter()->thenReturn($this->router);

        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        Phake::when($this->authorizationChecker)->isGranted(Phake::anyParameters())->thenReturn(true);

        $this->nodeTransformer = new NodeTransformer(
            $this->encryptionManager,
            $this->siteRepository,
            $this->statusRepository,
            $this->eventDispatcher,
            $this->authorizeEditionManager,
            $this->authorizationChecker
        );

        $this->nodeTransformer->setContext($this->transformerManager);
    }

    /**
     * Test transform
     */
    public function testTransformNotTransverse()
    {
        $facade = Phake::mock('OpenOrchestra\BaseApi\Facade\FacadeInterface');

        Phake::when($this->transformer)->transform(Phake::anyParameters())->thenReturn($facade);
        $area = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        $areas = new ArrayCollection();
        $areas->add($area);

        Phake::when($this->node)->getAreas()->thenReturn($areas);
        $facade = $this->nodeTransformer->transform($this->node);

        $this->assertInstanceOf('OpenOrchestra\ApiBundle\Facade\NodeFacade', $facade);
        $this->assertArrayHasKey('_self_form', $facade->getLinks());
        $this->assertArrayHasKey('_self_duplicate', $facade->getLinks());
        $this->assertArrayHasKey('_self_version', $facade->getLinks());
        $this->assertArrayHasKey('_language_list', $facade->getLinks());
        $this->assertArrayHasKey('_self_status_change', $facade->getLinks());
        $this->assertArrayHasKey('_block_list', $facade->getLinks());
        Phake::verify($this->router, Phake::times(11))->generate(Phake::anyParameters());
        Phake::verify($this->transformer)->transform($area, $this->node);
        Phake::verify($this->siteRepository)->findOneBySiteId(Phake::anyParameters());
    }

    /**
     * Test transform
     */
    public function testTransformTransverse()
    {
        $facade = Phake::mock('OpenOrchestra\BaseApi\Facade\FacadeInterface');

        Phake::when($this->transformer)->transform(Phake::anyParameters())->thenReturn($facade);
        $area = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        $areas = new ArrayCollection();
        $areas->add($area);

        Phake::when($this->node)->getAreas()->thenReturn($areas);
        Phake::when($this->node)->getNodeId()->thenReturn(NodeInterface::TRANSVERSE_NODE_ID);

        $facade = $this->nodeTransformer->transform($this->node);

        $this->assertInstanceOf('OpenOrchestra\ApiBundle\Facade\NodeFacade', $facade);
        $this->assertArrayHasKey('_self_form', $facade->getLinks());
        $this->assertArrayHasKey('_self_duplicate', $facade->getLinks());
        $this->assertArrayHasKey('_self_version', $facade->getLinks());
        $this->assertArrayHasKey('_language_list', $facade->getLinks());
        $this->assertArrayNotHasKey('_self_status_change', $facade->getLinks());
        $this->assertArrayHasKey('_block_list', $facade->getLinks());
        Phake::verify($this->router, Phake::times(9))->generate(Phake::anyParameters());
        Phake::verify($this->transformer)->transform($area, $this->node);
        Phake::verify($this->siteRepository)->findOneBySiteId(Phake::anyParameters());
    }

    /**
     * Test transformVersion
     */
    public function testTransformVersion()
    {
        $facade = $this->nodeTransformer->transformVersion($this->node);

        $this->assertInstanceOf('OpenOrchestra\ApiBundle\Facade\NodeFacade', $facade);
        $this->assertArrayHasKey('_self', $facade->getLinks());
        Phake::verify($this->router)->generate(Phake::anyParameters());
    }

    /**
     * @param mixed $facade
     * @param mixed $source
     * @param int   $searchCount
     * @param int   $setCount
     *
     * @dataProvider getChangeStatus
     */
    public function testReverseTransform($facade, $source, $searchCount, $setCount)
    {
        $this->nodeTransformer->reverseTransform($facade, $source);

        Phake::verify($this->statusRepository, Phake::times($searchCount))->find(Phake::anyParameters());
        Phake::verify($this->eventDispatcher, Phake::times($setCount))->dispatch(Phake::anyParameters());
    }

    /**
     * Test Exception transform with wrong object a parameters
     */
    public function testExceptionTransform()
    {
        $this->setExpectedException('OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException');
        $this->nodeTransformer->transform(Phake::mock('stdClass'));
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
        $this->nodeTransformer->reverseTransform($facade, $source);
    }

    /**
     * @return array
     */
    public function getChangeStatus()
    {
        $facadeA = Phake::mock('OpenOrchestra\ApiBundle\Facade\NodeFacade');

        $facadeB = Phake::mock('OpenOrchestra\ApiBundle\Facade\NodeFacade');
        $facadeB->statusId = 'fakeId';

        $node1 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $fromStatus = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($fromStatus)->getId()->thenReturn('fromStatus');
        Phake::when($node1)->getStatus()->thenReturn($fromStatus);

        $node2 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $fromStatus = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($fromStatus)->getId()->thenReturn('fromStatus');
        Phake::when($node2)->getStatus()->thenReturn($fromStatus);

        $node3 = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $fromStatus = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($fromStatus)->getId()->thenReturn('fromStatus');
        Phake::when($node3)->getStatus()->thenReturn($fromStatus);

        return array(
            array($facadeA, null, 0, 0),
            array($facadeA, $node1, 0, 0),
            array($facadeB, $node2, 1, 1),
            array($facadeB, $node3, 1, 1),
        );
    }

    /**
     * Test getName
     */
    public function testGetName()
    {
        $this->assertSame('node', $this->nodeTransformer->getName());
    }
}
