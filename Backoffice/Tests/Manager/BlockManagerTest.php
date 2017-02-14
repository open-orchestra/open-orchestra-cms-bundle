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

    /**
     * Set up the test
     */
    public function setUp()
    {
        $blockClass = 'OpenOrchestra\ModelBundle\Document\Block';
        $generateFormManager = Phake::mock('OpenOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager');
        Phake::when($generateFormManager)->getDefaultConfiguration(Phake::anyParameters())->thenReturn(array());
        $fixedParameter = array();

        $this->manager = new BlockManager(
            $blockClass,
            $generateFormManager,
            $fixedParameter
        );
    }

    /**
     * @param string $component
     * @param string $siteId
     * @param string $language
     * @param bool   $isTransverse
     *
     * @dataProvider provideBlockAttribute
     */
    public function testInitializeBlock(
        $component,
        $siteId,
        $language,
        $isTransverse
    ) {

        $block = $this->manager->initializeBlock($component, $siteId, $language, $isTransverse);
        $this->assertInstanceOf(BlockInterface::class, $block);
        $this->assertEquals($block->getComponent(), $component);
        $this->assertEquals($block->getSiteId(), $siteId);
        $this->assertEquals($block->getLanguage(), $language);
        $this->assertEquals($block->isTransverse(), $isTransverse);
    }

    /**
     * @return array
     */
    public function provideBlockAttribute()
    {
        return array(
            array('test', '2', 'fr', true),
            array('video', '2', 'fr', true),
            array('video', '2', 'fr', false),
            array('video', '2', 'fr', false),
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
