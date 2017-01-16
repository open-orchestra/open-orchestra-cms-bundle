<?php

namespace OpenOrchestra\Workflow\Tests\Form\Type\Component;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Workflow\Form\Type\Component\WorkflowTransitionsCollectionType;

/**
 * Class WorkflowTransitionsCollectionTypeTest
 */
class WorkflowTransitionsCollectionTypeTest extends AbstractBaseTestCase
{
    protected $form;
    protected $transitionTransformer;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->transitionTransformer = Phake::mock('OpenOrchestra\Workflow\Form\DataTransformer\ProfileTransitionsTransformer');
        $this->form = new WorkflowTransitionsCollectionType($this->transitionTransformer);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\AbstractType', $this->form);
    }

    /**
     * Test getParent
     */
    public function testGetParent()
    {
        $this->assertSame('choice', $this->form->getParent());
    }

    /**
     * Test getName
     */
    public function testGetName()
    {
        $this->assertSame('oo_workflow_transitions_collection', $this->form->getName());
    }

    public function testBuildForm()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilderInterface');

        $this->form->buildForm($builder, array());

        Phake::verify($builder)->addModelTransformer($this->transitionTransformer);
    }

    /**
     * Test configureOptions
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'expanded' => 'true',
            'multiple' => 'true',
            'required' => false,
            'statuses' => array(),
            'locale'   => 'en'
        ));
    }

    /**
     * test buildView
     */
    public function testBuildView()
    {
        $view = Phake::mock('Symfony\Component\Form\FormView');
        $form = Phake::mock('Symfony\Component\Form\FormInterface');

        $this->form->buildView(
            $view,
            $form,
            array(
                'statuses' => array(
                    $this->generateStatus('1'),
                    $this->generateStatus('2'),
                    $this->generateStatus('3'),
                ),
                'locale' => 'fakeLocale'
            )
        );

        $expected = array(
            '1' => 'label-1',
            '2' => 'label-2',
            '3' => 'label-3'
        );

        $this->assertSame($expected, $view->vars['statuses']);
    }

    /**
     * Generate a Phake status
     *
     * @param string $statusId
     *
     * @return Phake_IMock
     */
    protected function generateStatus($statusId)
    {
        $status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status)->getId()->thenReturn($statusId);
        Phake::when($status)->getLabel(Phake::anyParameters())->thenReturn('label-' . $statusId);

        return $status;
    }
}
