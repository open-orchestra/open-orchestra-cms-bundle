<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type\extension;

use OpenOrchestra\Backoffice\Form\Type\Extension\AccordionExtension;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class AccordionExtensionTest
 */
class AccordionExtensionTest extends AbstractBaseTestCase
{
    /**
     * @var AccordionExtension
     */
    protected $formExtension;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->formExtension = new AccordionExtension();
    }

    /**
     * Test buildView
     */
    public function testBuildView()
    {
        $resolvedFormType = Phake::mock('Symfony\Component\Form\ResolvedFormTypeInterface');
        Phake::when($resolvedFormType)->getName()->thenReturn('collection');

        $formConfig = Phake::mock('Symfony\Component\Form\FormConfigInterface');
        Phake::when($formConfig)->getType()->thenReturn($resolvedFormType);
        Phake::when($formConfig)->getOption('columns')->thenReturn(array('fakeColumn0', 'fakeColumn1', 'fakeNonExistingColumn'));

        $form = Phake::mock('Symfony\Component\Form\FormInterface');
        Phake::when($form)->getConfig()->thenReturn($formConfig);
        Phake::when($form)->getParent()->thenReturn($form);
        Phake::when($form)->has('fakeColumn0')->thenReturn(true);
        Phake::when($form)->has('fakeColumn1')->thenReturn(true);
        Phake::when($form)->has('fakeNonExistingColumn')->thenReturn(false);

        $formColumn0Config = Phake::mock('Symfony\Component\Form\FormConfigInterface');
        Phake::when($formColumn0Config)->getOption('label')->thenReturn('fakeColumn0Label');

        $formColumn0 = Phake::mock('Symfony\Component\Form\FormInterface');
        Phake::when($form)->get('fakeColumn0')->thenReturn($formColumn0);
        Phake::when($formColumn0)->getConfig()->thenReturn($formColumn0Config);
        Phake::when($formColumn0)->getData()->thenReturn('fakeColumn0Data');

        $formColumn1Config = Phake::mock('Symfony\Component\Form\FormConfigInterface');
        Phake::when($formColumn1Config)->getOption('label')->thenReturn('fakeColumn1Label');

        $formColumn1 = Phake::mock('Symfony\Component\Form\FormInterface');
        Phake::when($form)->get('fakeColumn1')->thenReturn($formColumn1);
        Phake::when($formColumn1)->getConfig()->thenReturn($formColumn1Config);
        Phake::when($formColumn1)->getData()->thenReturn('fakeColumn1Data');

        $formView = Phake::mock('Symfony\Component\Form\FormView');

        $this->formExtension->buildView($formView, $form, array());

        $this->assertEquals($formView->vars['columns'], array(
            'fakeColumn0Label' => 'fakeColumn0Data',
            'fakeColumn1Label' => 'fakeColumn1Data',
        ));
    }

    /**
     * Test getExtendedType
     */
    public function testGetExtendedType()
    {
        $this->assertSame('form', $this->formExtension->getExtendedType());
    }
}
