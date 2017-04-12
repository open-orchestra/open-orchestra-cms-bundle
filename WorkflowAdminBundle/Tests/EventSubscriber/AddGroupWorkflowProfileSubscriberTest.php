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

    /**
     * Set up the test
     */
    public function setUp()
    {
        $workflowProfile = Phake::mock('OpenOrchestra\ModelInterface\Model\WorkflowProfileInterface');
        Phake::when($workflowProfile)->getLabel(\Phake::anyParameters())->thenReturn('fakeWorkflowProfileLabel');
        $workflowProfileRepository = Phake::mock('OpenOrchestra\ModelBundle\Repository\WorkflowProfileRepository');
        Phake::when($workflowProfileRepository)->findAll()->thenReturn(array($workflowProfile));

        $contenType = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        Phake::when($contenType)->getContentTypeId()->thenReturn('fakeContentTypeId');
        Phake::when($contenType)->getName(Phake::anyParameters())->thenReturn('fakeName');
        $contentTypeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface');
        Phake::when($contentTypeRepository)->findAllNotDeletedInLastVersion(Phake::anyParameters())->thenReturn(array($contenType));

        $workflowProfileCollectionTransformer = Phake::mock('Symfony\Component\Form\DataTransformerInterface');
        $contextManager = Phake::mock('OpenOrchestra\Backoffice\Context\ContextManager');
        $translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        Phake::when($translator)->trans(Phake::anyParameters())->thenReturn('fakeTrans');

        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($site)->getContentTypes()->thenReturn(array($contenType));

        $siteRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface');
        Phake::when($siteRepository)->findOneBySiteId(Phake::anyParameters())->thenReturn($site);

        $this->subscriber = new AddGroupWorkflowProfileSubscriber(
            $workflowProfileRepository,
            $contentTypeRepository,
            $siteRepository,
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

        Phake::when($builder)->getAttribute(Phake::anyParameters())->thenReturn(array());
        Phake::when($builder)->getAttribute(Phake::anyParameters())->thenReturn(array());
        Phake::when($builder)->get(Phake::anyParameters())->thenReturn($builder);
        Phake::when($builder)->getData()->thenReturn(Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface'));
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
