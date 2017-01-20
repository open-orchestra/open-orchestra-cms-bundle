<?php

namespace OpenOrchestra\Backoffice\Tests\Manager;

use OpenOrchestra\Backoffice\Manager\BlockConfigurationManager;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class BlockConfigurationManagerTest
 */
class BlockConfigurationManagerTest extends AbstractBaseTestCase
{
    /**
     * @var BlockConfigurationManager
     */
    protected $manager;
    protected $translator;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        $blockConfiguration = array(
            'menu' => array(
                'category' => 'category',
                'name' => 'name',
                'description' =>  'description',
            )
        );
        Phake::when($this->translator)->trans('category')->thenReturn('category');
        Phake::when($this->translator)->trans('name')->thenReturn('name');
        Phake::when($this->translator)->trans('description')->thenReturn('description');
        Phake::when($this->translator)->trans(BlockConfigurationManager::DEFAULT_CATEGORY)->thenReturn(BlockConfigurationManager::DEFAULT_CATEGORY);

        $this->manager = new BlockConfigurationManager(
            $blockConfiguration,
            $this->translator
        );
    }

    /**
     * Test get block category
     */
    public function testGetBlockCategory()
    {
        $this->assertEquals('category', $this->manager->getBlockCategory('menu'));
        $this->assertEquals(BlockConfigurationManager::DEFAULT_CATEGORY, $this->manager->getBlockCategory('fake'));
    }

    /**
     * Test get block component name
     */
    public function testGetBlockComponentName()
    {
        $this->assertEquals('name', $this->manager->getBlockComponentName('menu'));
        Phake::when($this->translator)->trans('fake')->thenReturn('fake');
        $this->assertEquals('fake', $this->manager->getBlockComponentName('fake'));
    }

    /**
     * Test get block component description
     */
    public function testGetBlockComponentDescription()
    {
        $this->assertEquals('description', $this->manager->getBlockComponentDescription('menu'));
        $this->assertEquals('', $this->manager->getBlockComponentDescription('fake'));
    }
}
