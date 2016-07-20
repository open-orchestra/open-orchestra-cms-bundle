<?php

namespace OpenOrchestra\Backoffice\Tests\Manager;

use OpenOrchestra\Backoffice\Manager\TemplateManager;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\Model\AreaInterface;
use Phake;

/**
 * Class TemplateManagerTest
 */
class TemplateManagerTest extends AbstractBaseTestCase
{
    /**
     * @var TemplateManager
     */
    protected $manager;
    protected $contextManager;
    protected $areaManager;
    protected $siteId = 'fakeSiteId';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $templateClass = 'OpenOrchestra\ModelBundle\Document\Template';
        $areaClass = 'OpenOrchestra\ModelBundle\Document\Area';

        $rootAreaClass = new $areaClass();
        $rootAreaClass->setAreaType(AreaInterface::TYPE_ROOT);
        $rootAreaClass->setAreaId(AreaInterface::ROOT_AREA_ID);
        $rootAreaClass->setLabel(AreaInterface::ROOT_AREA_LABEL);

        $this->contextManager = Phake::mock('OpenOrchestra\Backoffice\Context\ContextManager');
        $this->areaManager = Phake::mock('OpenOrchestra\Backoffice\Manager\AreaManager');
        Phake::when($this->contextManager)->getCurrentSiteId()->thenReturn($this->siteId);
        Phake::when($this->areaManager)->initializeNewAreaRoot()->thenReturn($rootAreaClass);

        $this->manager = new TemplateManager(
            $this->contextManager,
            $templateClass,
            $this->areaManager
        );
    }

    /**
     * Test initialize new template
     */
    public function testInitializeNewTemplate()
    {
        $template = $this->manager->initializeNewTemplate();

        Phake::verify($this->areaManager)->initializeNewAreaRoot();
        $this->assertEquals($template->getSiteId(), $this->siteId);

        $area = $template->getArea();
        $this->assertEquals($area->getLabel(), AreaInterface::ROOT_AREA_LABEL);
        $this->assertEquals($area->getAreaId(), AreaInterface::ROOT_AREA_ID);
        $this->assertEquals($area->getAreaType(), AreaInterface::TYPE_ROOT);
    }
}
