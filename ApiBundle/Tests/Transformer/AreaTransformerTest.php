<?php

namespace OpenOrchestra\ApiBundle\Tests\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\ApiBundle\Facade\AreaFacade;
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
    protected $areaManager;
    protected $otherNode;
    protected $language;
    protected $router;
    protected $block;
    protected $node;
    protected $area;
    protected $currentSiteManager;
    protected $authorizationChecker;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->language = 'fr';

        $this->area = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        Phake::when($this->area)->getAreaId()->thenReturn($this->areaId);

        $this->block = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');

        $this->node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->node)->getNodeId()->thenReturn($this->currentNodeId);
        Phake::when($this->node)->getId()->thenReturn($this->nodeMongoId);
        Phake::when($this->node)->getBlock(Phake::anyParameters())->thenReturn($this->block);
        Phake::when($this->node)->getLanguage(Phake::anyParameters())->thenReturn($this->language);

        $this->otherNode = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->otherNode)->getBlock(Phake::anyParameters())->thenReturn($this->block);
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        Phake::when($this->nodeRepository)->findInLastVersion(Phake::anyParameters())
            ->thenReturn($this->otherNode);

        $this->transformer = Phake::mock('OpenOrchestra\ApiBundle\Transformer\BlockTransformer');
        $this->router = Phake::mock('Symfony\Component\Routing\RouterInterface');
        Phake::when($this->router)->generate(Phake::anyParameters())->thenReturn('route');
        $this->transformerManager = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerManager');
        Phake::when($this->transformerManager)->get(Phake::anyParameters())->thenReturn($this->transformer);
        Phake::when($this->transformerManager)->getRouter()->thenReturn($this->router);

        $this->areaManager = Phake::mock('OpenOrchestra\Backoffice\Manager\AreaManager');

        $this->currentSiteManager = Phake::mock('OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface');
        Phake::when($this->currentSiteManager)->getCurrentSiteId()->thenReturn('fakeId');

        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        Phake::when($this->authorizationChecker)->isGranted(Phake::anyParameters())->thenReturn(true);

        $this->areaTransformer = new AreaTransformer($this->facadeClass, $this->nodeRepository, $this->areaManager, $this->currentSiteManager,$this->authorizationChecker);

        $this->areaTransformer->setContext($this->transformerManager);
    }

    /**
     * @param string|null $parentAreaId
     *
     * @dataProvider provideParentAreaId
     */
    public function testTransform($parentAreaId = null)
    {
        Phake::when($this->otherNode)->getNodeId()->thenReturn('otherNodeId');
        Phake::when($this->otherNode)->getId()->thenReturn('otherMongoId');
        $blockFacade = new BlockFacade();
        Phake::when($this->transformer)->transform(Phake::anyParameters())->thenReturn($blockFacade);

        $area = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        Phake::when($area)->getLabel()->thenReturn('label');
        Phake::when($area)->getAreaId()->thenReturn('areaId');
        Phake::when($area)->getHtmlClass()->thenReturn('html_class');
        Phake::when($area)->getAreas()->thenReturn(new ArrayCollection());
        Phake::when($area)->getBlocks()->thenReturn(array(
            array('nodeId' => 0, 'blockId' => 0),
            array('nodeId' => 'root', 'blockId' => 0),
            array('nodeId' => $this->currentNodeId, 'blockId' => 0),
        ));

        $areaFacade = $this->areaTransformer->transform($area, $this->node, $parentAreaId);

        $this->assertInstanceOf('OpenOrchestra\ApiBundle\Facade\AreaFacade', $areaFacade);
        $this->assertArrayHasKey('_self_form', $areaFacade->getLinks());
        $this->assertArrayHasKey('_self_block', $areaFacade->getLinks());
        $this->assertArrayHasKey('_self', $areaFacade->getLinks());
        $this->assertArrayHasKey('_self_delete', $areaFacade->getLinks());
        Phake::verify($this->router, Phake::times(4))->generate(Phake::anyParameters());
        Phake::verify($this->transformer)->transform(
            $this->block,
            true,
            $this->currentNodeId,
            0,
            'areaId',
            0,
            $this->nodeMongoId
        );
        Phake::verify($this->transformer)->transform(
            $this->block,
            false,
            'otherNodeId',
            0,
            'areaId',
            1,
            'otherMongoId'
        );
        Phake::verify($this->transformer)->transform(
            $this->block,
            true,
            $this->currentNodeId,
            0,
            'areaId',
            2,
            $this->nodeMongoId
        );
    }

    /**
     * @return array
     */
    public function provideParentAreaId()
    {
        return array(
            array('main'),
            array(null),
        );
    }

    /**
     * @param string|null $parentAreaId
     *
     * @dataProvider provideParentAreaId
     */
    public function testTransformFromTemplate($parentAreaId = null)
    {
        $template = Phake::mock('OpenOrchestra\ModelInterface\Model\TemplateInterface');
        Phake::when($template)->getTemplateId()->thenReturn('templateId');

        $area = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        Phake::when($area)->getLabel()->thenReturn('label');
        Phake::when($area)->getAreaId()->thenReturn('areaId');
        Phake::when($area)->getHtmlClass()->thenReturn('html_class');
        Phake::when($area)->getAreas()->thenReturn(new ArrayCollection());

        $areaFacade = $this->areaTransformer->transformFromTemplate($area, $template, $parentAreaId);

        $this->assertInstanceOf('OpenOrchestra\ApiBundle\Facade\AreaFacade', $areaFacade);
        $this->assertArrayHasKey('_self_form', $areaFacade->getLinks());
        $this->assertArrayHasKey('_self', $areaFacade->getLinks());
        $this->assertArrayHasKey('_self_delete', $areaFacade->getLinks());
        Phake::verify($this->router, Phake::times(3))->generate(Phake::anyParameters());
    }

    /**
     * @param string $nodeId
     * @param int    $blockId
     *
     * @dataProvider provideNodeAndBlockId
     */
    public function testReverseTransform($nodeId, $blockId)
    {
        $blockFacade = new BlockFacade();
        $blockFacade->nodeId = $nodeId;
        $blockFacade->blockId = $blockId;

        $facade = new AreaFacade();
        $facade->addBlock($blockFacade);

        Phake::when($this->transformer)->reverseTransformToArray(Phake::anyParameters())
            ->thenReturn(array('nodeId' => $nodeId, 'blockId' => $blockId));

        $this->areaTransformer->reverseTransform($facade, $this->area, $this->node);

        Phake::verify($this->transformer)->reverseTransformToArray($blockFacade, $this->node);
        Phake::verify($this->area)->setBlocks(array(
            0 => array('nodeId' => $nodeId, 'blockId' => $blockId)
        ));

        $siteId = $this->currentSiteManager->getCurrentSiteId();
        Phake::verify($this->nodeRepository)->findInLastVersion($nodeId, $this->language, $siteId);
        Phake::verify($this->block)->addArea(array('nodeId' => $this->nodeMongoId, 'areaId' => $this->areaId));
        Phake::verify($this->areaManager, Phake::times(1))->deleteAreaFromBlock(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideNodeAndBlockId()
    {
        return array(
            array('root', 1),
            array('root', 5),
            array('page_home', 3),
            array('fixture_full', 8),
        );
    }

    /**
     * @param string $nodeId
     * @param int    $blockId
     *
     * @dataProvider provideNodeAndBlockId
     */
    public function testReverseTransformWithCurrentNodeBlock($nodeId, $blockId)
    {
        $blockFacade = new BlockFacade();
        $blockFacade->nodeId = $nodeId;
        $blockFacade->blockId = $blockId;

        $facade = new AreaFacade();
        $facade->addBlock($blockFacade);

        Phake::when($this->transformer)->reverseTransformToArray(Phake::anyParameters())
            ->thenReturn(array('nodeId' => 0, 'blockId' => $blockId));

        $this->areaTransformer->reverseTransform($facade, $this->area, $this->node);

        Phake::verify($this->transformer)->reverseTransformToArray($blockFacade, $this->node);
        Phake::verify($this->area)->setBlocks(array(
            0 => array('nodeId' => 0, 'blockId' => $blockId)
        ));
        Phake::verify($this->node)->getBlock($blockId);
        Phake::verify($this->block)->addArea(array('nodeId' => $this->nodeMongoId, 'areaId' => $this->areaId));
        Phake::verify($this->nodeRepository, Phake::never())->find(Phake::anyParameters());
    }

    /**
     * Test getName
     */
    public function testGetName()
    {
        $this->assertSame('area', $this->areaTransformer->getName());
    }
}
