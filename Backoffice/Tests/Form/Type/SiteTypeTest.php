<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Form\Type\SiteType;

/**
 * Class SiteTypeTest
 */
class SiteTypeTest extends AbstractBaseTestCase
{
    /**
     * @var SiteType
     */
    protected $form;

    protected $siteClass = 'oo_site';
    protected $translator;
    protected $templateManager;
    protected $languages = array('en', 'fr');

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        Phake::when($this->translator)->trans(Phake::anyParameters())->thenReturn('foo');
        $this->templateManager = Phake::mock('OpenOrchestra\Backoffice\Manager\TemplateManager');

        $this->form = new SiteType($this->siteClass, $this->translator, $this->templateManager);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\AbstractType', $this->form);
    }

    /**
     * Test name
     */
    public function testName()
    {
        $this->assertSame('oo_site', $this->form->getName());
    }

    /**
     * Test builder
     */
    public function testBuilder()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($builder)->add(Phake::anyParameters())->thenReturn($builder);
        Phake::when($builder)->addEventSubscriber(Phake::anyParameters())->thenReturn($builder);

        $this->form->buildForm($builder, array());

        Phake::verify($builder, Phake::times(8))->add(Phake::anyParameters());
        Phake::verify($this->translator, Phake::times(2))->trans(Phake::anyParameters());
        Phake::verify($builder, Phake::times(2))->addEventSubscriber(Phake::anyParameters());
    }

    /**
     * Test resolver
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(
            array(
                'data_class' => $this->siteClass,
                'group_enabled' => true,
                'group_render' => array(
                    'information' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_backoffice.form.website.group.information',
                    ),
                    'template_set' => array(
                        'rank' => 1,
                        'label' => 'open_orchestra_backoffice.form.website.group.template_set',
                    ),
                    'seo' => array(
                        'rank' => 2,
                        'label' => 'open_orchestra_backoffice.form.website.group.seo',
                    ),
                    'content' => array(
                        'rank' => 3,
                        'label' => 'open_orchestra_backoffice.form.website.group.content',
                    ),
                    'alias' => array(
                        'rank' => 4,
                        'label' => 'open_orchestra_backoffice.form.website.group.alias',
                    ),
                ),
                'sub_group_render' => array(
                    'property' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_backoffice.form.website.sub_group.property',
                    ),
                    'block' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_backoffice.form.website.sub_group.block',
                    ),
                    'meta' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_backoffice.form.website.sub_group.meta',
                    ),
                    'sitemap' => array(
                        'rank' => 1,
                        'label' => 'open_orchestra_backoffice.form.website.sub_group.sitemap',
                    ),
                    'robot' => array(
                        'rank' => 2,
                        'label' => 'open_orchestra_backoffice.form.website.sub_group.robot',
                    ),
                    'alias' => array(
                        'rank' => 2,
                        'label' => 'open_orchestra_backoffice.form.website.sub_group.robot',
                    ),
                ),
            )
        );
    }
}
