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

    protected $response;
    protected $documentManager;
    protected $displayBlockManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->response = Phake::mock('Symfony\Component\HttpFoundation\Response');
        $this->displayBlockManager = Phake::mock('PHPOrchestra\CMSBundle\DisplayBlock\DisplayBlockManager');
        Phake::when($this->displayBlockManager)->showBack(Phake::anyParameters())->thenReturn($this->response);

        $this->documentManager = Phake::mock('PHPOrchestra\CMSBundle\Document\DocumentManager');

        $this->transformer = new NodeTypeTransformer($this->documentManager, $this->displayBlockManager);
    }

    /**
     * test with load method
     */
    public function testTransformWithLoadMethod()
    {
        $htmlData = 'Some html data';
        Phake::when($this->response)->getContent()->thenReturn($htmlData);

        $areaId = 'testId';
        $blockId = 'blockId';
        $nodeId = 'nodeId';
        $component = 'Sample';
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

        $block = Phake::mock('PHPOrchestra\CMSBundle\Model\Block');
        Phake::when($block)->getComponent()->thenReturn($component);
        $blocks = array($blockId => $block);
        $blocksGroup = Phake::mock('Mandango\Group\EmbeddedGroup');
        Phake::when($blocksGroup)->getSaved()->thenReturn($blocks);
        Phake::when($blocksGroup)->all()->thenReturn($blocks);

        $node = Phake::mock('PHPOrchestra\CMSBundle\Model\Node');
        Phake::when($node)->getAreas()->thenReturn($areas);
        Phake::when($node)->getBlocks()->thenReturn($blocksGroup);

        Phake::when($this->documentManager)->getDocumentById(Phake::anyParameters())->thenReturn($node);

        $returnedNode = $this->transformer->transform($node);

        $this->assertSame($node, $returnedNode);
        Phake::verify($node)->getAreas();
        Phake::verify($node, Phake::times(2))->getBlocks();
        Phake::verify($this->documentManager)->getDocumentById('Node', $nodeId);
        Phake::verify($node)->setAreas(
            json_encode(array(
                'areas' => array(
                    array(
                        'areaId' => $areaId,
                        'classes' => '',
                        'blocks' => array(
                            array(
                                'method' => NodeTypeTransformer::BLOCK_LOAD,
                                'nodeId' => $nodeId,
                                'blockId' => $blockId,
                                'ui-model' => array(
                                    'label' => $component,
                                    'html' => $htmlData,
                                )
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
        Phake::when($this->response)->getContent()->thenReturn($htmlData);

        $areaId = 'testId';
        $blockId = 'blockId';
        $component = 'Sample';
        $attributNews = 'test';
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
                                'method' => NodeTypeTransformer::BLOCK_GENERATE,
                                'component' => $component,
                                'attributs_news' => $attributNews,
                                'ui-model' => array(
                                    'label' => $component,
                                    'html' => $htmlData,
                                ),
                            )
                        ),
                        'ui-model' => array('label' => $areaId),
                    )
                )
            ))
        );
    }

    /**
     * Test with empty blocks
     */
    public function testTransformWithEmptyBlocks()
    {
        $htmlData = 'Some html data';
        Phake::when($this->response)->getContent()->thenReturn($htmlData);

        $areaId = 'testId';
        $blockId = 'blockId';
        $component = 'sample';
        $nodeId = 0;
        $area = array(
            'areaId' => $areaId,
            'classes' => array(),
            'blocks' => array()
        );
        $areas = array($area);

        $blocksGroup = Phake::mock('Mandango\Group\EmbeddedGroup');
        Phake::when($blocksGroup)->getSaved()->thenReturn(array());

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
                        'ui-model' => array('label' => $areaId),
                    )
                )
            ))
        );
    }
    
    /**
     * Test with generate method
     */
    public function testReverseTransformWithGenerateMethod()
    {
        $areaId = 'testId';
        $blockId = 'blockId';
        $component = 'Sample';
        $attributNews = 'test';
        $nodeId = 0;
        $htmlData = 'Some html data';
        
        $areas = json_encode(array(
            'areas' => array(
                array(
                    'areaId' => $areaId,
                    'classes' => '',
                    'blocks' => array(
                        array(
                            'method' => NodeTypeTransformer::BLOCK_GENERATE,
                            'component' => $component,
                            'attributs_news' => $attributNews,
                            'ui-model' => array(
                                'label' => $component,
                                'html' => $htmlData,
                            ),
                        )
                    ),
                    'ui-model' => array('label' => $areaId),
                )
            )
        ));
        

        $blocksGroup = Phake::mock('Mandango\Group\EmbeddedGroup');
        Phake::when($blocksGroup)->getSaved()->thenReturn(array());
        Phake::when($blocksGroup)->count()->thenReturn(0);
        
        $node = Phake::mock('PHPOrchestra\CMSBundle\Model\Node');
        $block = Phake::mock('PHPOrchestra\CMSBundle\Model\Block');
        
        Phake::when($block)->setComponent(Phake::anyParameters())->thenReturn($block);
        Phake::when($block)->setAttributes(Phake::anyParameters())->thenReturn($block);
                
        Phake::when($node)->getAreas()->thenReturn($areas);
        Phake::when($node)->getBlocks()->thenReturn($blocksGroup);
        Phake::when($this->documentManager)->createDocument('Block')->thenReturn($block);
        Phake::when($node)->removeBlocks(Phake::anyParameters())->thenReturn(null);
        
        $returnedNode = $this->transformer->reverseTransform($node);
        
        Phake::verify($node)->setAreas(
            array(
                array(
                    'areaId' => $areaId,
                    'classes' => array(''),
                    'blocks' => array(
                        array(
                            'nodeId' => 0,
                            'blockId' => 0,
                        )
                    )
                )
            )
        );
    }

    /**
     * Test with generate method
     */
    public function testReverseTransformWithLoadMethod()
    {
        $areaId = 'testId';
        $blockId = 'blockId';
        $component = 'Sample';
        $attributNews = 'test';
        $nodeId = 0;
        $htmlData = 'Some html data';
        
        $areas = json_encode(array(
            'areas' => array(
                array(
                    'areaId' => $areaId,
                    'classes' => '',
                    'blocks' => array(
                        array(
                            'method' => NodeTypeTransformer::BLOCK_LOAD,
                            'nodeId' => $areaId,
                            'blockId' => $blockId,
                            'ui-model' => array(
                                'label' => $component,
                                'html' => $htmlData,
                            ),
                        )
                    ),
                    'ui-model' => array('label' => $areaId),
                )
            )
            
        ));
        

        $blocksGroup = Phake::mock('Mandango\Group\EmbeddedGroup');
        Phake::when($blocksGroup)->getSaved()->thenReturn(array());
        Phake::when($blocksGroup)->count()->thenReturn(0);
        
        $node = Phake::mock('PHPOrchestra\CMSBundle\Model\Node');
        $block = Phake::mock('PHPOrchestra\CMSBundle\Model\Block');
        
        Phake::when($block)->setComponent(Phake::anyParameters())->thenReturn($block);
        Phake::when($block)->setAttributes(Phake::anyParameters())->thenReturn($block);
                
        Phake::when($node)->getAreas()->thenReturn($areas);
        Phake::when($node)->getBlocks()->thenReturn($blocksGroup);
        Phake::when($this->documentManager)->createDocument('Block')->thenReturn($block);
        Phake::when($node)->removeBlocks(Phake::anyParameters())->thenReturn(null);
        
        $returnedNode = $this->transformer->reverseTransform($node);
        
        Phake::verify($node)->setAreas(
            array(
                array(
                    'areaId' => $areaId,
                    'classes' => array(''),
                    'blocks' => array(
                        array(
                            'nodeId' => $nodeId,
                            'blockId' => $blockId,
                        )
                    )
                )
            )
        );
    }
}
