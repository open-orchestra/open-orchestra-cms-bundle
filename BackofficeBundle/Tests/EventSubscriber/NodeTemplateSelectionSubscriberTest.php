<?php

namespace OpenOrchestra\BackofficeBundle\Tests\EventSubscriber;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\BackofficeBundle\EventSubscriber\NodeTemplateSelectionSubscriber;
use OpenOrchestra\ModelInterface\Model\TemplateInterface;
use Symfony\Component\Form\FormEvents;

/**
 * Class NodeTemplateSelectionSubscriberTest
 */
class NodeTemplateSelectionSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var NodeTemplateSelectionSubscriber
     */
    protected $subscriber;

    protected $form;
    protected $event;
    protected $node;
    protected $nodeManager;
    protected $templateRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($this->form)->add(Phake::anyParameters())->thenReturn($this->form);

        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($this->event)->getForm()->thenReturn($this->form);

        $this->nodeManager = Phake::mock('OpenOrchestra\BackofficeBundle\Manager\NodeManager');
        $this->node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $this->templateRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\TemplateRepositoryInterface');

        $this->subscriber = new NodeTemplateSelectionSubscriber($this->nodeManager,$this->templateRepository);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->subscriber);
    }

    /**
     * Test subscribed events
     */
    public function testEventSubscribed()
    {
        $this->assertArrayHasKey(FormEvents::PRE_SUBMIT, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(FormEvents::PRE_SET_DATA, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test the build form with a new node
     *
     * @param array $templates
     * @param array $expectedResult
     *
     * @dataProvider getTemplate
     */
    public function testPreSetDataOnNewNode($templates, $expectedResult)
    {
        Phake::when($this->node)->getId()->thenReturn(null);
        Phake::when($this->event)->getData()->thenReturn($this->node);
        Phake::when($this->templateRepository)->findByDeleted(Phake::anyParameters())->thenReturn($templates);

        Phake::when($this->form)->get(Phake::anyParameters())->thenReturn($this->form);
        $this->subscriber->preSetData($this->event);

        Phake::verify($this->event->getForm())->add('nodeTemplateSelection', 'form', array(
            'virtual' => true,
            'label' => 'open_orchestra_backoffice.form.node.template_selection.name',
            'mapped' => false,
            'label_attr' => array('class' => 'one-needed'),
            'required' => false,
            'attr' => array(
                'help_text' => 'open_orchestra_backoffice.form.node.template_selection.helper',
            )
        ));

        Phake::verify($this->event->getForm())->add('templateId', 'choice', array(
            'choices' => $expectedResult,
            'required' => false,
            'label' => 'open_orchestra_backoffice.form.node.template_id'
        ));

        Phake::verify($this->event->getForm())->add('nodeSource', 'oo_node_choice', array(
            'required' => false,
            'mapped' => false,
            'label' => 'open_orchestra_backoffice.form.node.node_source'
        ));
    }

    /**
     * Test the build form with an existing node
     *
     * @param array $templates
     * @param array $expectedResult
     *
     * @dataProvider getTemplate
     */
    public function testPreSetDataWithExistingNode($templates, $expectedResult)
    {
        Phake::when($this->node)->getId()->thenReturn('fakeId');
        Phake::when($this->event)->getData()->thenReturn($this->node);
        Phake::when($this->templateRepository)->findByDeleted(Phake::anyParameters())->thenReturn($templates);

        $this->subscriber->preSetData($this->event);

        Phake::verify($this->form, Phake::never())->add(Phake::anyParameters());
    }

    /**
     * Templates provider
     *
     * @return array
     */
    public function getTemplate()
    {
        $id0 = 'fakeId0';
        $name0 = 'fakeName0';
        $id1 = 'fakeId1';
        $name1 = 'fakeName1';

        $template0 = Phake::mock('OpenOrchestra\ModelInterface\Model\TemplateInterface');
        Phake::when($template0)->getTemplateId()->thenReturn($id0);
        Phake::when($template0)->getName()->thenReturn($name0);

        $template1 = Phake::mock('OpenOrchestra\ModelInterface\Model\TemplateInterface');
        Phake::when($template1)->getTemplateId()->thenReturn($id1);
        Phake::when($template1)->getName()->thenReturn($name1);

        return array(
            array(
                array($template0, $template1), array($id0 => $name0, $id1 => $name1)
            )
        );
    }

    /**
     * @param array             $data
     * @param TemplateInterface $template
     *
     * @dataProvider getDataTemplate
     */
    public function testPreSubmitTemplate($data, $template)
    {
        $emptyCollection = Phake::mock('Doctrine\Common\Collections\ArrayCollection');
        Phake::when($emptyCollection)->count()->thenReturn(0);

        $templateChoiceContainer = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($templateChoiceContainer)->getAreas()->thenReturn($emptyCollection);
        Phake::when($templateChoiceContainer)->getBlocks()->thenReturn($emptyCollection);

        Phake::when($templateChoiceContainer)->getData()->thenReturn($data);
        Phake::when($this->form)->getData()->thenReturn($templateChoiceContainer);

        Phake::when($this->event)->getData()->thenReturn($data);
        Phake::when($this->templateRepository)->findOneByTemplateId(Phake::anyParameters())->thenReturn($template);

        $this->subscriber->preSubmit($this->event);

        Phake::verify($templateChoiceContainer, Phake::times((null === $template)? 0: 1))->setAreas((null !== $template)? $template->getAreas() : '');
        Phake::verify($templateChoiceContainer, Phake::times((null === $template)? 0: 1))->setBlocks((null !== $template)? $template->getBlocks() : '');
    }

    /**
     * @param array             $data
     * @param TemplateInterface $template
     *
     * @dataProvider getDataTemplate
     */
    public function testPreSubmitWithExistingAreas($data, $template)
    {
        $fullCollection = Phake::mock('Doctrine\Common\Collections\ArrayCollection');
        Phake::when($fullCollection)->count()->thenReturn(1);

        $templateChoiceContainer = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($templateChoiceContainer)->getAreas()->thenReturn($fullCollection);
        Phake::when($templateChoiceContainer)->getBlocks()->thenReturn($fullCollection);

        Phake::when($templateChoiceContainer)->getData()->thenReturn($data);
        Phake::when($this->form)->getData()->thenReturn($templateChoiceContainer);

        Phake::when($this->event)->getData()->thenReturn($data);
        Phake::when($this->templateRepository)->findOneByTemplateId(Phake::anyParameters())->thenReturn($template);

        $this->subscriber->preSubmit($this->event);

        Phake::verify($templateChoiceContainer, Phake::never())->setAreas(Phake::anyParameters());
        Phake::verify($templateChoiceContainer, Phake::never())->setBlocks(Phake::anyParameters());
    }

    /**
     * Templates provider
     *
     * @return array
     */
    public function getDataTemplate()
    {
        $areas = Phake::mock('Doctrine\Common\Collections\Collection');
        $blocks = Phake::mock('Doctrine\Common\Collections\Collection');

        $template = Phake::mock('OpenOrchestra\ModelInterface\Model\TemplateInterface');
        Phake::when($template)->getAreas()->thenReturn($areas);
        Phake::when($template)->getBlocks()->thenReturn($blocks);

        return array(
            array(array('nodeTemplateSelection' => array('templateId' => 1)), $template),
            array(array('nodeTemplateSelection' => array('templateId' => 1)), null),
        );
    }

    /**
     * @param array             $data
     * @param TemplateInterface $template
     *
     * @dataProvider getDataTemplate
     */
    public function testPreSubmitWithExistingNode($data, $template)
    {
        $templateChoiceContainer = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');

        Phake::when($templateChoiceContainer)->getData()->thenReturn($data);
        Phake::when($templateChoiceContainer)->getId()->thenReturn('nodeId');
        Phake::when($this->form)->getData()->thenReturn($templateChoiceContainer);

        Phake::when($this->event)->getData()->thenReturn($data);
        Phake::when($this->templateRepository)->findOneByTemplateId(Phake::anyParameters())->thenReturn($template);

        $this->subscriber->preSubmit($this->event);

        Phake::verify($templateChoiceContainer, Phake::never())->setAreas(Phake::anyParameters());
        Phake::verify($templateChoiceContainer, Phake::never())->setBlocks(Phake::anyParameters());
    }

    /**
     * Test presubmit on new node
     */
    public function testPreSubmitWithNewNode()
    {
        $nodeSourceId = 'root';
        Phake::when($this->node)->getId()->thenReturn(null);
        Phake::when($this->form)->getData()->thenReturn($this->node);
        Phake::when($this->event)->getData()->thenReturn(array('nodeTemplateSelection' => array('nodeSource' => $nodeSourceId)));

        $this->subscriber->preSubmit($this->event);

        Phake::verify($this->nodeManager)->hydrateNodeFromNodeId($this->node, $nodeSourceId);
    }

    /**
     * Test presubmit on old node
     */
    public function testPreSubmitWithOldNode()
    {
        $nodeSourceId = 'root';
        Phake::when($this->node)->getId()->thenReturn($nodeSourceId);
        Phake::when($this->form)->getData()->thenReturn($this->node);
        Phake::when($this->event)->getData()->thenReturn(array('nodeTemplateSelection' => array('nodeSource' => $nodeSourceId)));

        $this->subscriber->preSubmit($this->event);

        Phake::verify($this->nodeManager, Phake::never())->hydrateNodeFromNodeId(Phake::anyParameters());
    }

    /**
     * Test presubmit with no source
     */
    public function testPreSubmitWithNoSource()
    {
        Phake::when($this->node)->getId()->thenReturn(null);
        Phake::when($this->form)->getData()->thenReturn($this->node);
        Phake::when($this->event)->getData()->thenReturn(array());

        $this->subscriber->preSubmit($this->event);

        Phake::verify($this->nodeManager, Phake::never())->hydrateNodeFromNodeId(Phake::anyParameters());
    }
}
