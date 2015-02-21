<?php

namespace OpenOrchestra\BackofficeBundle\Test\Form\Type;

use Phake;
use OpenOrchestra\BackofficeBundle\Form\Type\ExistingBlockChoiceType;

/**
 * Class ExistingBlockChoiceTypeTest
 */
class ExistingBlockChoiceTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExistingBlockChoiceType
     */
    protected $form;

    protected $builder;
    protected $nodeRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($this->builder)->add(Phake::anyParameters())->thenReturn($this->builder);

        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');

        $this->form = new ExistingBlockChoiceType($this->nodeRepository);
    }

    /**
     * Test name
     */
    public function testName()
    {
        $this->assertSame('existing_block', $this->form->getName());
    }

    /**
     * Test with no node
     */
    public function testWithNoNode()
    {
        $this->form->buildForm($this->builder, array());

        Phake::verify($this->builder, Phake::never())->add();
        Phake::verify($this->nodeRepository)->findAll();
    }

    /**
     * @return array
     */
    public function provideBlockInfos()
    {
        return array(
            array('sample', 'Main', 1),
            array('news', 'Welcome', 3),
            array('search', 'fixture_full', 5),
        );
    }

    /**
     * @param string $component
     * @param string $nodeId
     * @param int    $blockIndex
     *
     * @dataProvider provideBlockInfos
     */
    public function testWithMultipleNodeAndMultipleBlock($component, $nodeId, $blockIndex)
    {
        $title = 'blockTitle';
        $otherBlockIndex = $blockIndex + 1;
        $otherNodeId = $nodeId . 'other';
        $block = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($block)->getComponent()->thenReturn($component);

        $otherBlock = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($otherBlock)->getComponent()->thenReturn($component);
        Phake::when($otherBlock)->getAttributes()->thenReturn(array('title' => $title));

        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node)->getBlocks()->thenReturn(array(
            $blockIndex => $block,
            $otherBlockIndex => $otherBlock,
        ));
        Phake::when($node)->getNodeId()->thenReturn($nodeId);
        $otherNode = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($otherNode)->getBlocks()->thenReturn(array(
            $blockIndex => $block,
            $otherBlockIndex => $otherBlock,
        ));
        Phake::when($otherNode)->getNodeId()->thenReturn($otherNodeId);

        Phake::when($this->nodeRepository)->findAll()->thenReturn(array($node, $otherNode));

        $this->form->buildForm($this->builder, array());

        Phake::verify($this->builder)->add('existingBlock', 'choice', array(
            'required' => false,
            'choices' => array(
                $nodeId => array(
                    $nodeId . ':' . $blockIndex => $component,
                    $nodeId . ':' . $otherBlockIndex => $title,
                ),
                $otherNodeId => array(
                    $otherNodeId . ':' . $blockIndex => $component,
                    $otherNodeId . ':' . $otherBlockIndex => $title,
                ),
            ),
            'label' => 'open_orchestra_backoffice.form.area.existing_block'
        ));
        Phake::verify($this->nodeRepository)->findAll();
    }
}
