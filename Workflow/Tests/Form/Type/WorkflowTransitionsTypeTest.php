<?php

namespace OpenOrchestra\Workflow\Tests\Form\Type;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Workflow\Form\Type\WorkflowTransitionsType;

/**
 * Class WorkflowTransitionsTypeTest
 */
class WorkflowTransitionsTypeTest extends AbstractBaseTestCase
{
    protected $form;
    protected $statusRepository;
    protected $statuses;
    protected $locale = 'fakeLocale';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->statusRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface');
        $this->statuses = array($this->generateStatus('1'), $this->generateStatus('2'), $this->generateStatus('3'));
        Phake::when($this->statusRepository)->findNotOutOfWorkflow(Phake::anyParameters())->thenReturn($this->statuses);

        $contextManager = Phake::mock('OpenOrchestra\Backoffice\Context\ContextBackOfficeInterface');
        Phake::when($contextManager)->getBackOfficeLanguage()->thenReturn($this->locale);
        $this->form = new WorkflowTransitionsType($this->statusRepository, $contextManager);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\AbstractType', $this->form);
    }

    /**
     * Test getName
     */
    public function testGetName()
    {
        $this->assertSame('oo_workflow_transitions', $this->form->getName());
    }

    /**
     * Test getParent
     */
    public function testGetParent()
    {
        $this->assertSame('collection', $this->form->getParent());
    }

    /**
     * Test configureOptions
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'type'         => 'oo_workflow_profile_transitions',
            'allow_add'    => false,
            'allow_delete' => false,
            'options'      => array(
                'statuses' => $this->statuses,
                'locale'   => $this->locale
            )
        ));
    }

    /**
     * test buildView
     */
    public function testBuildView()
    {
        $view = Phake::mock('Symfony\Component\Form\FormView');
        $form = Phake::mock('Symfony\Component\Form\FormInterface');

        $this->form->buildView($view, $form, array());

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
