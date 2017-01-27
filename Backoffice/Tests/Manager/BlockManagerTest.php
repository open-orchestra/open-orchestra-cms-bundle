<?php

namespace OpenOrchestra\Backoffice\Tests\Manager;

use OpenOrchestra\Backoffice\Manager\BlockManager;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Phake;

/**
 * Class BlockManagerTest
 */
class BlockManagerTest extends AbstractBaseTestCase
{
    /**
     * @var BlockManager
     */
    protected $manager;
    protected $displayBlockManager;
    protected $blockParameterManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $blockClass = 'OpenOrchestra\ModelBundle\Document\Block';
        $this->displayBlockManager = Phake::mock('OpenOrchestra\DisplayBundle\DisplayBlock\DisplayBlockManager');
        $this->blockParameterManager = Phake::mock('OpenOrchestra\BackofficeBundle\StrategyManager\BlockParameterManager');
        $generateFormManager = Phake::mock('OpenOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager');
        Phake::when($generateFormManager)->getDefaultConfiguration(Phake::anyParameters())->thenReturn(array());
        $fixedParameter = array();

        $this->manager = new BlockManager(
            $blockClass,
            $this->displayBlockManager,
            $this->blockParameterManager,
            $generateFormManager,
            $fixedParameter
        );
    }

    /**
     * @param string $component
     * @param string $siteId
     * @param string $language
     * @param bool   $isTransverse
     * @param bool   $isPublic
     * @param array  $blockParameter
     *
     * @dataProvider provideBlockAttribute
     */
    public function testInitializeBlock(
        $component,
        $siteId,
        $language,
        $isTransverse,
        $isPublic,
        array $blockParameter
    ) {

        Phake::when($this->displayBlockManager)->isPublic(Phake::anyParameters())->thenReturn($isPublic);
        Phake::when($this->blockParameterManager)->getBlockParameter(Phake::anyParameters())->thenReturn($blockParameter);
        $block = $this->manager->initializeBlock($component, $siteId, $language, $isTransverse);
        $this->assertInstanceOf(BlockInterface::class, $block);
        $this->assertEquals($block->getComponent(), $component);
        $this->assertEquals($block->getSiteId(), $siteId);
        $this->assertEquals($block->getLanguage(), $language);
        $this->assertEquals($block->isTransverse(), $isTransverse);
        $this->assertEquals($block->isPrivate(), !$isPublic);
        $this->assertEquals($block->getParameter(), $blockParameter);
    }

    /**
     * @return array
     */
    public function provideBlockAttribute()
    {
        return array(
            array('test', '2', 'fr', true, false, array()),
            array('video', '2', 'fr', true, true, array()),
            array('video', '2', 'fr', false, true, array()),
            array('video', '2', 'fr', false, true, array('compo' => 'fakeCompo')),
        );
    }

    /**
     * Test create to translate block
     */
    public function testCreateToTranslateBlock()
    {
        $block = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        $label = 'fake_label';
        $language = 'fake_language';
        $newLanguage = 'fr';
        Phake::when($block)->getLabel()->thenReturn($label);
        Phake::when($block)->getLanguage()->thenReturn($language);

        $newBlock = $this->manager->createToTranslateBlock($block, $newLanguage);
        $this->assertInstanceOf('OpenOrchestra\ModelInterface\Model\BlockInterface', $newBlock);
        Phake::verify($newBlock)->setLanguage($newLanguage);
        Phake::verify($newBlock)->setLabel($label."[".$language."]");
    }
}
