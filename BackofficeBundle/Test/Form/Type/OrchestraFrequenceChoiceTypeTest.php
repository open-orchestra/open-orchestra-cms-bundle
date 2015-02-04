<?php

namespace PHPOrchestra\BackofficeBundle\Test\Form\Type;

use Phake;
use PHPOrchestra\BackofficeBundle\Form\Type\OrchestraFrequenceChoiceType;

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
            'always' => 'php_orchestra_backoffice.form.node.changefreq.always',
            'hourly' => 'php_orchestra_backoffice.form.node.changefreq.hourly',
            'daily' => 'php_orchestra_backoffice.form.node.changefreq.daily',
            'weekly' => 'php_orchestra_backoffice.form.node.changefreq.weekly',
            'monthly' => 'php_orchestra_backoffice.form.node.changefreq.monthly',
            'yearly' => 'php_orchestra_backoffice.form.node.changefreq.yearly',
            'never' => 'php_orchestra_backoffice.form.node.changefreq.never'
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
