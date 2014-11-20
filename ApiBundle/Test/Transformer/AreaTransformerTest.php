<?php

namespace PHPOrchestra\ApiBundle\Test\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use Phake;
use PHPOrchestra\ApiBundle\Facade\AreaFacade;
use PHPOrchestra\ApiBundle\Facade\BlockFacade;
use PHPOrchestra\ApiBundle\Transformer\AreaTransformer;

/**
 * Class AreaTransformerTest
 */
class AreaTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AreaTransformer
     */
    protected $areaTransformer;

    protected $currentNodeId = 'currentNodeId';
    protected $nodeMongoId = 'nodeMongoId';
    protected $transformerManager;
    protected $areaId = 'areaId';
    protected $nodeRepository;
    protected $transformer;
    protected $otherNode;
    protected $router;
    protected $block;
    protected $node;
    protected $area;
    protected $areaManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->area = Phake::mock('PHPOrchestra\ModelBundle\Model\AreaInterface');
        Phake::when($this->area)->getAreaId()->thenReturn($this->areaId);

        $this->block = Phake::mock('PHPOrchestra\ModelBundle\Model\BlockInterface');

        $this->node = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
        Phake::when($this->node)->getNodeId()->thenReturn($this->currentNodeId);
        Phake::when($this->node)->getId()->thenReturn($this->nodeMongoId);
        Phake::when($this->node)->getBlock(Phake::anyParameters())->thenReturn($this->block);

        $this->otherNode = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
        Phake::when($this->otherNode)->getBlock(Phake::anyParameters())->thenReturn($this->block);
        $this->nodeRepository = Phake::mock('PHPOrchestra\ModelBundle\Repository\NodeRepository');
        Phake::when($this->nodeRepository)->findOneByNodeIdAndSiteIdAndLastVersion(Phake::anyParameters())
            ->thenReturn($this->otherNode);

        $this->transformer = Phake::mock('PHPOrchestra\ApiBundle\Transformer\BlockTransformer');
        $this->router = Phake::mock('Symfony\Component\Routing\RouterInterface');
        Phake::when($this->router)->generate(Phake::anyParameters())->thenReturn('route');
        $this->transformerManager = Phake::mock('PHPOrchestra\ApiBundle\Transformer\TransformerManager');
        Phake::when($this->transformerManager)->get(Phake::anyParameters())->thenReturn($this->transformer);
        Phake::when($this->transformerManager)->getRouter()->thenReturn($this->router);

        $this->areaManager = Phake::mock('PHPOrchestra\BackofficeBundle\Manager\AreaManager');

        $this->areaTransformer = new AreaTransformer($this->nodeRepository, $this->areaManager);

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

        $area = Phake::mock('PHPOrchestra\ModelBundle\Document\Area');
        Phake::when($area)->getLabel()->thenReturn('label');
        Phake::when($area)->getAreaId()->thenReturn('areaId');
        Phake::when($area)->getClasses()->thenReturn(array('area_class'));
        Phake::when($area)->getHtmlClass()->thenReturn('html_class');
        Phake::when($area)->getAreas()->thenReturn(new ArrayCollection());
        Phake::when($area)->getBlocks()->thenReturn(array(
            array('nodeId' => 0, 'blockId' => 0),
            array('nodeId' => 'root', 'blockId' => 0),
            array('nodeId' => $this->currentNodeId, 'blockId' => 0),
        ));

        $areaFacade = $this->areaTransformer->transform($area, $this->node, $parentAreaId);

        $this->assertInstanceOf('PHPOrchestra\ApiBundle\Facade\AreaFacade', $areaFacade);
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
        $template = Phake::mock('PHPOrchestra\ModelBundle\Model\TemplateInterface');
        Phake::when($template)->getTemplateId()->thenReturn('templateId');

        $area = Phake::mock('PHPOrchestra\ModelBundle\Document\Area');
        Phake::when($area)->getLabel()->thenReturn('label');
        Phake::when($area)->getAreaId()->thenReturn('areaId');
        Phake::when($area)->getClasses()->thenReturn(array('area_class'));
        Phake::when($area)->getHtmlClass()->thenReturn('html_class');
        Phake::when($area)->getAreas()->thenReturn(new ArrayCollection());

        $areaFacade = $this->areaTransformer->transformFromTemplate($area, $template, $parentAreaId);

        $this->assertInstanceOf('PHPOrchestra\ApiBundle\Facade\AreaFacade', $areaFacade);
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
        Phake::verify($this->nodeRepository)->findOneByNodeIdAndSiteIdAndLastVersion($nodeId);
        Phake::verify($this->block)->addArea(array('nodeId' => $this->currentNodeId, 'areaId' => $this->areaId));
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
        Phake::verify($this->block)->addArea(array('nodeId' => $this->currentNodeId, 'areaId' => $this->areaId));
        Phake::verify($this->nodeRepository, Phake::never())->findOneByNodeIdAndSiteIdAndLastVersion(Phake::anyParameters());
    }
}
