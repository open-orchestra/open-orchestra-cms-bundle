<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\WorkflowAdminBundle\EventSubscriber\AddGroupWorkflowProfileSubscriber;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\GroupBundle\GroupFormEvents;

/**
 * Class BlockTypeSubscriberTest
 */
class AddGroupWorkflowProfileSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var AddGroupWorkflowProfileSubscriber
     */
    protected $subscriber;

    protected $form;
    protected $event;
    protected $block;
    protected $fixedParameters;
    protected $contenType;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $workflowProfile = Phake::mock('OpenOrchestra\ModelInterface\Model\WorkflowProfileInterface');
        Phake::when($workflowProfile)->getLabel(\Phake::anyParameters())->thenReturn('fakeWorkflowProfileLabel');
        $workflowProfileRepository = Phake::mock('OpenOrchestra\ModelBundle\Repository\WorkflowProfileRepository');
        Phake::when($workflowProfileRepository)->findAll()->thenReturn(array($workflowProfile));

        $this->contenType = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        Phake::when($this->contenType)->getContentTypeId()->thenReturn('fakeContentTypeId');
        Phake::when($this->contenType)->getName(Phake::anyParameters())->thenReturn('fakeName');
        $contentTypeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface');
        Phake::when($contentTypeRepository)->findAllNotDeletedInLastVersion(Phake::anyParameters())->thenReturn(array($this->contenType));

        $workflowProfileCollectionTransformer = Phake::mock('Symfony\Component\Form\DataTransformerInterface');
        $contextManager = Phake::mock('OpenOrchestra\Backoffice\Context\ContextManager');
        $translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        Phake::when($translator)->trans(Phake::anyParameters())->thenReturn('fakeTrans');

        $this->subscriber = new AddGroupWorkflowProfileSubscriber(
            $workflowProfileRepository,
            $contentTypeRepository,
            $workflowProfileCollectionTransformer,
            $contextManager,
            $translator
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
        $this->assertArrayHasKey(GroupFormEvents::GROUP_FORM_CREATION, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test addWorkflowProfile
     */
    public function testAddWorkflowProfile()
    {
        $event = Phake::mock('OpenOrchestra\GroupBundle\Event\GroupFormEvent');
        $builder = Phake::mock('Symfony\Component\Form\FormBuilderInterface');
        $group = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');
        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');

        Phake::when($site)->getContentTypes()->thenReturn(array($this->contenType));
        Phake::when($group)->getSite()->thenReturn($site);
        Phake::when($builder)->getAttribute(Phake::anyParameters())->thenReturn(array());
        Phake::when($builder)->getAttribute(Phake::anyParameters())->thenReturn(array());
        Phake::when($builder)->get(Phake::anyParameters())->thenReturn($builder);
        Phake::when($builder)->getData()->thenReturn($group);
        Phake::when($event)->getBuilder()->thenReturn($builder);

        $this->subscriber->addWorkflowProfile($event);
        Phake::verify($builder, Phake::times(2))->setAttribute(Phake::anyParameters());
        Phake::verify($builder, Phake::times(1))->add('workflow_profile_collections', 'oo_check_list_collection', array(
            'label' => false,
            'configuration' => array(
                'default' => array(
                     'row' => array('fakeWorkflowProfileLabel'),
                     'column' => array(
                         'node' => 'fakeTrans',
                         'fakeContentTypeId' => 'fakeName',
                     ),
                 ),
            ),
            'group_id' => 'profile',
            'sub_group_id' => 'backoffice',
            'required' => false
        ));

        Phake::verify($builder, Phake::times(1))->addModelTransformer(Phake::anyParameters());
    }
}
