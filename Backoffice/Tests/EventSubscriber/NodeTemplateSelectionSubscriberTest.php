<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\EventSubscriber\NodeTemplateSelectionSubscriber;
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
    protected $contextManager;
    protected $siteRepository;

    protected $siteId = 'fakeSiteId';
    protected $templateSet = 'fakeTemplateSet';
    protected $templateSetData = array(
        'templates' => array(
            'default' => array('label' => 'default'),
            'full_page' => array('label' => 'full_page'),
        )
    );

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($this->form)->add(Phake::anyParameters())->thenReturn($this->form);

        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($this->event)->getForm()->thenReturn($this->form);

        $this->node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');

        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($site)->getTemplateSet()->thenReturn($this->templateSet);

        $this->nodeManager = Phake::mock('OpenOrchestra\Backoffice\Manager\NodeManager');

        $this->contextManager = Phake::mock('OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface');
        Phake::when($this->contextManager)->getCurrentSiteId()->thenReturn($this->siteId);

        $this->siteRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface');
        Phake::when($this->siteRepository)->findOneBySiteId($this->siteId)->thenReturn($site);

        $this->templateManager = Phake::mock('OpenOrchestra\Backoffice\Manager\TemplateManager');
        Phake::when($this->templateManager)->getTemplateSetParameters()->thenReturn(array(
            $this->templateSet => $this->templateSetData
        ));

        $this->subscriber = new NodeTemplateSelectionSubscriber(
            $this->nodeManager,
            $this->contextManager,
            $this->siteRepository,
            $this->templateManager
        );
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
     * Test the build form
     *
     * @param string|null $id
     * @param integer     $nbrCall
     *
     * @dataProvider getNodeIdAndTemplateSetParameters
     */
    public function testPreSetData($id, $nbrCall)
    {
        $choices = array(
            'default' => 'default',
            'full_page' => 'full_page',
        );

        Phake::when($this->node)->getId()->thenReturn($id);
        Phake::when($this->event)->getData()->thenReturn($this->node);

        Phake::when($this->form)->get(Phake::anyParameters())->thenReturn($this->form);
        $this->subscriber->preSetData($this->event);

        Phake::verify($this->event->getForm(), Phake::times($nbrCall))->add('nodeTemplateSelection', 'form', array(
            'virtual' => true,
            'label' => 'open_orchestra_backoffice.form.node.template_selection.name',
            'mapped' => false,
            'label_attr' => array('class' => 'one-needed'),
            'required' => false,
            'attr' => array(
                'help_text' => 'open_orchestra_backoffice.form.node.template_selection.helper',
            )
        ));

        Phake::verify($this->event->getForm(), Phake::times($nbrCall))->add('nodeSource', 'oo_node_choice', array(
            'required' => false,
            'mapped' => false,
            'label' => 'open_orchestra_backoffice.form.node.node_source'
        ));

        Phake::verify($this->event->getForm(), Phake::times($nbrCall))->add('template', 'choice', array(
            'choices' => $choices,
            'required' => false,
            'label' => 'open_orchestra_backoffice.form.node.template'
        ));
    }

    /**
     * Node Id provider
     *
     * @return array
     */
    public function getNodeIdAndTemplateSetParameters()
    {
        return array(
            array(null, 1),
            array('fakeNodeId', 0),
        );
    }

    /**
     * @param array       $data
     * @param string|null $id
     * @param integer     $nbrCall
     *
     * @dataProvider getNodeIdAndData
     */
    public function testPreSubmit($data, $id, $nbrCall)
    {
        Phake::when($this->node)->getId()->thenReturn($id);

        Phake::when($this->event)->getData()->thenReturn($data);
        Phake::when($this->form)->getData()->thenReturn($this->node);

        $this->subscriber->preSubmit($this->event);
        Phake::verify($this->nodeManager, Phake::times($nbrCall))->hydrateNodeFromNodeId(Phake::anyParameters());
    }

    /**
     * Templates provider
     *
     * @return array
     */
    public function getNodeIdAndData()
    {
        return array(
            array(array('nodeTemplateSelection' => array('nodeSource' => 'fakeNodeId')), null, 1),
            array(array('nodeTemplateSelection' => array('nodeSource' => 'fakeNodeId')), 'fakeNodeId', 0),
            array(array(), null, 0),
        );
    }
}
