<?php

namespace PHPOrchestra\CMSBundle\Test\Form\DataTransformer;

use Phake;
use PHPOrchestra\CMSBundle\Form\DataTransformer\NodeTypeTransformer;

/**
 * Class NodeTypeTransformerTest
 */
class NodeTypeTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodeTypeTransformer
     */
    protected $transformer;

    protected $container;
    protected $controller;
    protected $jsonResponse;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->jsonResponse = Phake::mock('Symfony\Component\HttpFoundation\JsonResponse');
        $this->controller = Phake::mock('PHPOrchestra\CMSBundle\Controller\BlockController');
        Phake::when($this->controller)->getPreview(Phake::anyParameters())->thenReturn($this->jsonResponse);
        $this->container = Phake::mock('Symfony\Component\DependencyInjection\Container');
        Phake::when($this->container)->get('phporchestra_cms.blockcontroller')->thenReturn($this->controller);

        $this->transformer = new NodeTypeTransformer($this->container, true);
    }

    /**
     * test with load method
     */
    public function testTransformWithLoadMethod()
    {
        $htmlData = 'Some html data';
        $jsonString = json_encode(array('data' => $htmlData));
        Phake::when($this->jsonResponse)->getContent()->thenReturn($jsonString);

        $blocks = array();

        $areaId = 'testId';
        $blockId = 'blockId';
        $area = array(
            'areaId' => $areaId,
            'classes' => array(),
            'blocks' => array(
                array('blockId' => $blockId)
            )
        );
        $areas = array($area);

        $blocksGroup = Phake::mock('Mandango\Group\EmbeddedGroup');
        Phake::when($blocksGroup)->getSaved()->thenReturn($blocks);

        $node = Phake::mock('PHPOrchestra\CMSBundle\Model\Node');
        Phake::when($node)->getAreas()->thenReturn($areas);
        Phake::when($node)->getBlocks()->thenReturn($blocksGroup);

        $returnedNode = $this->transformer->transform($node);

        $this->assertSame($node, $returnedNode);
        Phake::verify($node)->getAreas();
        Phake::verify($node)->getBlocks();
        Phake::verify($node)->setAreas(
            json_encode(array(
                'areas' => array(
                    array(
                        'areaId' => $areaId,
                        'classes' => '',
                        'blocks' => array(
                            array(
                                'blockId' => $blockId,
                                'ui-model' => array(
                                    'label' => $blockId,
                                    'html' => $htmlData,
                                ),
                                'method' => NodeTypeTransformer::BLOCK_LOAD
                            )
                        ),
                        'ui-model' => array('label' => $areaId),
                    )
                )
            ))
        );
    }

    /**
     * Test with generate method
     */
    public function testTransformWithGenerateMethod()
    {
        $htmlData = 'Some html data';
        $jsonString = json_encode(array('data' => $htmlData));
        Phake::when($this->jsonResponse)->getContent()->thenReturn($jsonString);

        $areaId = 'testId';
        $blockId = 'blockId';
        $nodeId = 0;
        $area = array(
            'areaId' => $areaId,
            'classes' => array(),
            'blocks' => array(
                array(
                    'blockId' => $blockId,
                    'nodeId' => $nodeId,
                )
            )
        );
        $areas = array($area);

        $component = 'Sample';
        $attributNews = 'test';
        $blockRef = Phake::mock('PHPOrchestra\CMSBundle\Model\Block');
        Phake::when($blockRef)->getComponent()->thenReturn($component);
        Phake::when($blockRef)->getAttributes()->thenReturn(array('news' => $attributNews));

        $blocks = array(
            $blockId => $blockRef,
        );

        $blocksGroup = Phake::mock('Mandango\Group\EmbeddedGroup');
        Phake::when($blocksGroup)->getSaved()->thenReturn($blocks);

        $node = Phake::mock('PHPOrchestra\CMSBundle\Model\Node');
        Phake::when($node)->getAreas()->thenReturn($areas);
        Phake::when($node)->getBlocks()->thenReturn($blocksGroup);

        $returnedNode = $this->transformer->transform($node);

        $this->assertSame($node, $returnedNode);
        Phake::verify($node)->getAreas();
        Phake::verify($node)->getBlocks();
        Phake::verify($node)->setAreas(
            json_encode(array(
                'areas' => array(
                    array(
                        'areaId' => $areaId,
                        'classes' => '',
                        'blocks' => array(
                            array(
                                'ui-model' => array(
                                    'label' => $blockId,
                                    'html' => $htmlData,
                                ),
                                'method' => NodeTypeTransformer::BLOCK_GENERATE,
                                'component' => $component,
                                'attributs_news' => $attributNews
                            )
                        ),
                        'ui-model' => array('label' => $areaId),
                    )
                )
            ))
        );
    }
}
