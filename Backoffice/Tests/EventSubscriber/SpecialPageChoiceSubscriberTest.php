<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\Backoffice\EventSubscriber\SpecialPageChoiceSubscriber;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Phake;
use Symfony\Component\Form\FormEvents;

/**
 * Class SpecialPageChoiceSubscriberTest
 */
class SpecialPageChoiceSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /** @var SpecialPageChoiceSubscriber */
    protected $subscriber;
    protected $data;
    protected $event;
    protected $form;
    protected $nodeRepository;
    protected $specialPageList = array(
        'default' => 'fake_label',
    );

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = Phake::mock('Symfony\Component\Form\Form');
        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($this->event)->getForm()->thenReturn($this->form);
        Phake::when($this->form)->get(Phake::anyParameters())->thenReturn($this->form);
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        $currentSiteManager = Phake::mock('OpenOrchestra\Backoffice\Context\ContextBackOfficeInterface');

        $this->subscriber = new SpecialPageChoiceSubscriber(
            $this->nodeRepository,
            $currentSiteManager,
            $this->specialPageList
        );
    }

    /**
     * @param NodeInterface|null $data
     * @param array              $specialPagesNode
     * @param array              $expectedSpecialPageList
     *
     * @dataProvider provideSpecialPagesNode
     */
    public function testPreSetData($data, array $specialPagesNode, array $expectedSpecialPageList)
    {
        Phake::when($this->event)->getData()->thenReturn($data);
        Phake::when($this->nodeRepository)->findAllSpecialPage(Phake::anyParameters())->thenReturn($specialPagesNode);

        $this->subscriber->preSetData($this->event);

        Phake::verify($this->form)->add('specialPageName', 'choice', array(
            'label' => 'open_orchestra_backoffice.form.node.specialPageName',
            'choices' => $expectedSpecialPageList,
            'group_id' => 'properties',
            'sub_group_id' => 'properties',
            'required' => false,
        ));
    }

    /**
     * @return array
     */
    public function provideSpecialPagesNode()
    {
        $data = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($data)->getSpecialPageName()->thenReturn('default');

        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node)->getSpecialPageName()->thenReturn('default');

        return array(
            'with data' => array($data, array($node), $this->specialPageList),
            'without data' => array(null, array($node), array()),
        );
    }

    /**
     * Test subscribed events
     */
    public function testEventSubscribed()
    {
        $this->assertArrayHasKey(FormEvents::PRE_SET_DATA, $this->subscriber->getSubscribedEvents());
    }
}
