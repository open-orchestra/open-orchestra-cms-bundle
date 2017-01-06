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

        $formView = Phake::mock('Symfony\Component\Form\FormView');

        $this->formExtension->buildView($formView, $form, array());

        $this->assertEquals($formView->vars['attr']['class'], 'collection-sortable');
    }

    /**
     * Test getExtendedType
     */
    public function testGetExtendedType()
    {
        $this->assertSame('form', $this->formExtension->getExtendedType());
    }
}
