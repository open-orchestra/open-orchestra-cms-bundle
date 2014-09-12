<?php

namespace PHPOrchestra\BackofficeBundle\Test\Form\Type;

use Phake;
use PHPOrchestra\BackofficeBundle\Form\Type\OrchestraStatusType;
use PHPOrchestra\ModelBundle\Model\StatusableInterface;

/**
 * Class OrchestraStatusTypeTest
 */
class OrchestraStatusTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $form;

    /**
     * Set up the text
     */
    public function setUp()
    {
        $this->form = new OrchestraStatusType();
    }

    /**
     * Test Name
     */
    public function testName()
    {
        $this->assertSame('orchestra_status', $this->form->getName());
    }

    /**
     * Test Parent
     */
    public function testParent()
    {
        $this->assertSame('document', $this->form->getParent());
    }

    /**
     * Test the default options
     */
    public function testSetDefaultOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->form->setDefaultOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(array(
            'class' => 'PHPOrchestra\ModelBundle\Document\Status',
            'property' => 'labels',
        ));
    }
}
