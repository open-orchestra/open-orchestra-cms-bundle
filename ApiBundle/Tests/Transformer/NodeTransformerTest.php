<?php

namespace OpenOrchestra\ApiBundle\Tests\Transformer;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\ApiBundle\Transformer\NodeTransformer;

/**
 * Class NodeTransformerTest
 */
class NodeTransformerTest extends AbstractBaseTestCase
{
    /**
     * @var NodeTransformer
     */
    protected $nodeTransformer;

    protected $facadeClass = 'OpenOrchestra\ApiBundle\Facade\NodeFacade';
    protected $authorizationChecker;
    protected $transformerManager;
    protected $encryptionManager;
    protected $statusRepository;
    protected $nodeRepository;
    protected $eventDispatcher;
    protected $siteRepository;
    protected $transformer;
    protected $statusId;
    protected $router;
    protected $status;
    protected $node;
    protected $site;
    protected $businessRulesManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->eventDispatcher = Phake::mock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $this->node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($this->node)->getStatus()->thenReturn($status);
        Phake::when($status)->isPublishedState()->thenReturn(false);

        $siteAlias = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteAliasInterface');
        $this->site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($this->site)->getAliases()->thenReturn(array($siteAlias));

        $this->status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        $this->statusId = 'StatusId';
        Phake::when($this->status)->getId(Phake::anyParameters())->thenReturn($this->statusId);

        $this->encryptionManager = Phake::mock('OpenOrchestra\BaseBundle\Manager\EncryptionManager');

        $this->siteRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface');
        Phake::when($this->siteRepository)->findOneBySiteId(Phake::anyParameters())->thenReturn($this->site);

        $this->statusRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface');
        Phake::when($this->statusRepository)->find(Phake::anyParameters())->thenReturn($this->status);

        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');

        $this->transformer = Phake::mock('OpenOrchestra\ApiBundle\Transformer\BlockTransformer');
        $this->router = Phake::mock('Symfony\Component\Routing\RouterInterface');
        Phake::when($this->router)->generate(Phake::anyParameters())->thenReturn('route');

        $this->transformerManager = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerManager');
        Phake::when($this->transformerManager)->get(Phake::anyParameters())->thenReturn($this->transformer);
        Phake::when($this->transformerManager)->getRouter()->thenReturn($this->router);

        $groupContext = Phake::mock('OpenOrchestra\BaseApi\Context\GroupContext');
        Phake::when($this->transformerManager)->getGroupContext()->thenReturn($groupContext);
        Phake::when($groupContext)->hasGroup(Phake::anyParameters())->thenReturn(true);

        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        Phake::when($this->authorizationChecker)->isGranted(Phake::anyParameters())->thenReturn(true);

        $this->businessRulesManager = Phake::mock('OpenOrchestra\Backoffice\BusinessRules\BusinessRulesManager');
        Phake::when($this->businessRulesManager)->isGranted(Phake::anyParameters())->thenReturn(true);

        $this->nodeTransformer = new NodeTransformer(
            Phake::mock('Doctrine\Common\Cache\ArrayCache'),
            $this->facadeClass,
            $this->encryptionManager,
            $this->siteRepository,
            $this->statusRepository,
            $this->eventDispatcher,
            $this->authorizationChecker,
            $this->nodeRepository,
            $this->businessRulesManager
        );

        $this->nodeTransformer->setContext($this->transformerManager);
    }

    /**
     * Test transform
     */
    public function testTransform()
    {
        $facade = Phake::mock('OpenOrchestra\BaseApi\Facade\FacadeInterface');

        Phake::when($this->transformer)->transform(Phake::anyParameters())->thenReturn($facade);
        $area = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');

        Phake::when($this->node)->getAreas()->thenReturn(array($area));
        $facade = $this->nodeTransformer->transform($this->node);

        $this->assertInstanceOf('OpenOrchestra\ApiBundle\Facade\NodeFacade', $facade);
        Phake::verify($this->router, Phake::times(1))->generate(Phake::anyParameters());
        Phake::verify($this->transformer)->transform($area);
        Phake::verify($this->siteRepository, Phake::times(2))->findOneBySiteId(Phake::anyParameters());
    }

    /**
     * Test transform with update not granted
     */
    public function testTransformNotGranted()
    {
        $facade = Phake::mock('OpenOrchestra\BaseApi\Facade\FacadeInterface');
        Phake::when($this->authorizationChecker)->isGranted(Phake::anyParameters())->thenReturn(false);

        Phake::when($this->transformer)->transform(Phake::anyParameters())->thenReturn($facade);
        $area = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');

        Phake::when($this->node)->getAreas()->thenReturn(array($area));
        $facade = $this->nodeTransformer->transform($this->node);

        $this->assertSame($facade->getRights()['can_read'], false);
        $this->assertInstanceOf('OpenOrchestra\ApiBundle\Facade\NodeFacade', $facade);
        Phake::verify($this->router, Phake::times(1))->generate(Phake::anyParameters());
        Phake::verify($this->transformer)->transform($area);
        Phake::verify($this->siteRepository, Phake::times(2))->findOneBySiteId(Phake::anyParameters());
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
        if (null !== $source) {
            Phake::verify($source, Phake::times($setCount))->setStatus(Phake::anyParameters());
        }
    }

    /**
     * Test Exception transform with wrong object a parameters
     */
    public function testExceptionTransform()
    {
        $this->expectException('OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException');
        $this->nodeTransformer->transform(Phake::mock('stdClass'));
    }

    /**
     * @return array
     */
    public function getChangeStatus()
    {
        $facadeA = Phake::mock('OpenOrchestra\ApiBundle\Facade\NodeFacade');

        $facadeB = Phake::mock('OpenOrchestra\ApiBundle\Facade\NodeFacade');
        $facadeStatus = Phake::mock('OpenOrchestra\WorkflowAdminBundle\Facade\StatusFacade');
        $facadeB->status = $facadeStatus;
        $facadeStatus->id = 'fakeId';

        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status)->getId()->thenReturn('otherFakeId');
        Phake::when($node)->getStatus()->thenReturn($status);

        return array(
            array($facadeA, null, 0, 0),
            array($facadeA, $node, 0, 0),
            array($facadeB, $node, 1, 1),
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
