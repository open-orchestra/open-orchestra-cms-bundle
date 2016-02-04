<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Manager;

use OpenOrchestra\BackofficeBundle\Manager\TemplateFlexManager;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class TemplateFlexManagerTest
 */
class TemplateFlexManagerTest extends AbstractBaseTestCase
{
    /**
     * @var TemplateFlexManager
     */
    protected $manager;
    protected $contextManager;
    protected $siteId = 'fakeSiteId';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $templateFlexClass = 'OpenOrchestra\ModelBundle\Document\TemplateFlex';
        $areaClass = 'OpenOrchestra\ModelBundle\Document\Area';

        $this->contextManager = Phake::mock('OpenOrchestra\Backoffice\Context\ContextManager');
        Phake::when($this->contextManager)->getCurrentSiteId()->thenReturn($this->siteId);

        $this->manager = new TemplateFlexManager(
            $this->contextManager,
            $templateFlexClass,
            $areaClass
        );
    }

    /**
     * Test initialize new template flex
     */
    public function testInitializeNewTemplateFlex()
    {
        $template = $this->manager->initializeNewTemplateFlex();

        $this->assertEquals($template->getSiteId(), $this->siteId);
        $this->assertCount(1, $template->getAreas());

        $area = $template->getAreas()[0];
        $this->assertEquals($area->getLabel(), TemplateFlexManager::DEFAULT_AREA_LABEL);
        $this->assertEquals($area->getAreaId(), TemplateFlexManager::DEFAULT_AREA_ID);
    }
}
