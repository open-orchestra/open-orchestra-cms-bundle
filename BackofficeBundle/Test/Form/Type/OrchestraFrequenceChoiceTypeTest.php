<?php

namespace OpenOrchestra\BackofficeBundle\Test\Form\Type;

use Phake;
use OpenOrchestra\BackofficeBundle\Form\Type\OrchestraFrequenceChoiceType;

/**
 * Class OrchestraFrequenceChoiceTypeTest
 */
class OrchestraFrequenceChoiceTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OrchestraFrequenceChoiceType
     */
    protected $orchestraFrequenceChoiceType;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->orchestraFrequenceChoiceType = new OrchestraFrequenceChoiceType();
    }

    /**
     * test default options
     */
    public function testSetDefaultOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->orchestraFrequenceChoiceType->setDefaultOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(
            array('choices' => array(
            'always' => 'open_orchestra_backoffice.form.changefreq.always',
            'hourly' => 'open_orchestra_backoffice.form.changefreq.hourly',
            'daily' => 'open_orchestra_backoffice.form.changefreq.daily',
            'weekly' => 'open_orchestra_backoffice.form.changefreq.weekly',
            'monthly' => 'open_orchestra_backoffice.form.changefreq.monthly',
            'yearly' => 'open_orchestra_backoffice.form.changefreq.yearly',
            'never' => 'open_orchestra_backoffice.form.changefreq.never'
        ))
        );
    }

    /**
     * Test parent
     */
    public function testGetParent()
    {
        $this->assertEquals('choice', $this->orchestraFrequenceChoiceType->getParent());
    }

    /**
     * test Name
     */
    public function testGetName()
    {
        $this->assertEquals('orchestra_frequence_choice', $this->orchestraFrequenceChoiceType->getName());
    }
}
