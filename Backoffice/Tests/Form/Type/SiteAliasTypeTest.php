<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Form\Type\SiteAliasType;

/**
 * Class SiteAliasTypeTest
 */
class SiteAliasTypeTest extends AbstractBaseTestCase
{
    /**
     * @var SiteAliasType
     */
    protected $form;

    protected $siteAliasClass = 'site_alias';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = new SiteAliasType($this->siteAliasClass);
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
        $this->assertSame('oo_site_alias', $this->form->getName());
    }

    /**
     * Test builder
     */
    public function testBuilder()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($builder)->add(Phake::anyParameters())->thenReturn($builder);

        $this->form->buildForm($builder, array());

        Phake::verify($builder, Phake::times(13))->add(Phake::anyParameters());
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
                'data_class' => $this->siteAliasClass,
                'columns' => array('domain', 'language', 'prefix'),
                'group_enabled' => true,
                'group_render' => array(
                    'information' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_backoffice.form.alias.group.information',
                    ),
                    'seo' => array(
                        'rank' => 1,
                        'label' => 'open_orchestra_backoffice.form.alias.group.seo',
                    ),
                ),
                'sub_group_render' => array(
                    'property' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_backoffice.form.alias.sub_group.property',
                    ),
                    'meta' => array(
                        'rank' => 1,
                        'label' => 'open_orchestra_backoffice.form.alias.sub_group.meta',
                    ),
                    'google_marker' => array(
                        'rank' => 2,
                        'label' => 'open_orchestra_backoffice.form.alias.sub_group.google_marker',
                    ),
                    'xiti' => array(
                        'rank' => 3,
                        'label' => 'open_orchestra_backoffice.form.alias.sub_group.xiti',
                    ),
                ),
            )
        );
    }
}
