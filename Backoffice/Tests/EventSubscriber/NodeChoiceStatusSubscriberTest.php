<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\Backoffice\EventSubscriber\NodeChoiceStatusSubscriber;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use Phake;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\Backoffice\EventSubscriber\NodeThemeSelectionSubscriber;

/**
 * Class NodeChoiceStatusSubscriberTest
 */
class NodeChoiceStatusSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var NodeThemeSelectionSubscriber
     */
    protected $subscriber;

    protected $authorizeStatusChangeManager;
    protected $event;
    protected $object;
    protected $form;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->object = Phake::mock('OpenOrchestra\ModelBundle\Document\Node');
        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        $this->form = Phake::mock('Symfony\Component\Form\FormInterface');

        $this->authorizeStatusChangeManager = Phake::mock('OpenOrchestra\BackofficeBundle\StrategyManager\AuthorizeStatusChangeManager');

        Phake::when($this->event)->getData()->thenReturn($this->object);
        Phake::when($this->event)->getForm()->thenReturn($this->form);

        $this->subscriber = new NodeChoiceStatusSubscriber($this->authorizeStatusChangeManager);
    }

    /**
     * Test if method is present
     */
    public function testCallable()
    {
        $this->assertTrue(is_callable(array($this->subscriber, 'preSetData')));
    }

    /**
     * Test pre set data with new node
     */
    public function testPreSetDataWithoutId()
    {
        Phake::when($this->object)->getId()->thenReturn(null);

        $this->subscriber->preSetData($this->event);

        Phake::verify($this->form, Phake::never())->add(Phake::anyParameters());
    }

    /**
     * Test pre set data
     *
     * @param StatusInterface $status
     * @param bool            $isGranted
     * @param array           $expected
     *
     * @dataProvider provideStatus
     */
    public function testPreSetData($status, $isGranted, array $expected)
    {
        Phake::when($this->authorizeStatusChangeManager)->isGranted(Phake::anyParameters())->thenReturn($isGranted);
        Phake::when($this->object)->getId()->thenReturn('fakeId');
        Phake::when($this->object)->getStatus()->thenReturn($status);

        $this->subscriber->preSetData($this->event);

        Phake::verify($this->form)->add('status', 'oo_status_choice', array(
                'embedded' => true,
                'label' => 'open_orchestra_backoffice.form.node.status',
                'group_id' => 'properties',
                'sub_group_id' => 'publication',
                'choices' => $expected
        ));
    }

    /**
     * @return array
     */
    public function provideStatus()
    {
        $status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        $status2 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        $role = Phake::mock('OpenOrchestra\ModelInterface\Model\RoleInterface');
        $fromRoles = array($role);
        Phake::when($status)->getFromRoles()->thenReturn($fromRoles);
        Phake::when($role)->getToStatus()->thenReturn($status2);
        Phake::when($role)->getFromStatus()->thenReturn($status);

        return array(
            array($status, true, array($status, $status2)),
            array($status, false, array($status)),
        );
    }
}
