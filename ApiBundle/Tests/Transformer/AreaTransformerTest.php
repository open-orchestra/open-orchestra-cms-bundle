<?php

namespace OpenOrchestra\ApiBundle\Tests\Transformer;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\ApiBundle\Facade\BlockFacade;
use OpenOrchestra\ApiBundle\Transformer\AreaTransformer;

/**
 * Class AreaTransformerTest
 */
class AreaTransformerTest extends AbstractBaseTestCase
{
    /**
     * @var AreaTransformer
     */
    protected $areaTransformer;

    protected $facadeClass = 'OpenOrchestra\ApiBundle\Facade\AreaFacade';
    protected $currentNodeId = 'currentNodeId';
    protected $nodeMongoId = 'nodeMongoId';
    protected $transformerManager;
    protected $areaId = 'areaId';
    protected $nodeRepository;
    protected $transformer;
    protected $language;
    protected $router;
    protected $block;
    protected $node;
    protected $area;
    protected $authorizationChecker;
    protected $siteId = 'fakeSiteId';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->language = 'fr';

        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');

        $this->block = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        $this->area = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        Phake::when($this->area)->getAreaId()->thenReturn($this->areaId);
        Phake::when($this->area)->getBlocks()->thenReturn(array($this->block, $this->block, $this->block));

        $this->node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->node)->getNodeId()->thenReturn($this->currentNodeId);
        Phake::when($this->node)->getId()->thenReturn($this->nodeMongoId);
        Phake::when($this->node)->getLanguage(Phake::anyParameters())->thenReturn($this->language);
        Phake::when($this->node)->getSiteId(Phake::anyParameters())->thenReturn($this->siteId);

        $this->transformer = Phake::mock('OpenOrchestra\ApiBundle\Transformer\BlockTransformer');
        $this->router = Phake::mock('Symfony\Component\Routing\RouterInterface');
        Phake::when($this->router)->generate(Phake::anyParameters())->thenReturn('route');
        $this->transformerManager = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerManager');
        Phake::when($this->transformerManager)->get(Phake::anyParameters())->thenReturn($this->transformer);
        Phake::when($this->transformerManager)->getRouter()->thenReturn($this->router);

        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        Phake::when($this->authorizationChecker)->isGranted(Phake::anyParameters())->thenReturn(true);

        $this->areaTransformer = new AreaTransformer(
            $this->facadeClass,
            $this->nodeRepository,
            $this->authorizationChecker
        );

        $this->areaTransformer->setContext($this->transformerManager);
    }

    /**
     * test transform
     */
    public function testTransform()
    {
        $blockFacade = new BlockFacade();
        Phake::when($this->transformer)->transform(Phake::anyParameters())->thenReturn($blockFacade);

        $areaFacade = $this->areaTransformer->transform($this->area, $this->node, 'fakeAreaId');

        $this->assertInstanceOf('OpenOrchestra\ApiBundle\Facade\AreaFacade', $areaFacade);
        $this->assertArrayHasKey('_block_list', $areaFacade->getLinks());
        $this->assertArrayHasKey('_self_update_block_position', $areaFacade->getLinks());
//        $this->assertArrayHasKey('_self', $areaFacade->getLinks());
        Phake::verify($this->router, Phake::times(2))->generate(Phake::anyParameters());
        Phake::verify($this->transformer)->transform(
            $this->block,
            0
       );
        Phake::verify($this->transformer)->transform(
            $this->block,
            1
        );
        Phake::verify($this->transformer)->transform(
            $this->block,
            2
        );
    }

    /**
     * Test getName
     */
    public function testGetName()
    {
        $this->assertSame('area', $this->areaTransformer->getName());
    }
}
