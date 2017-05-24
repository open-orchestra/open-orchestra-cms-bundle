<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\EventSubscriber\SiteSubscriber;
use Symfony\Component\Form\FormEvents;

/**
 * Class ChangeNodeContentSubscriberTest
 */
class SiteSubscriberTest extends AbstractBaseTestCase
{
    protected $subscriber;
    protected $siteRepository;
    protected $siteDomain1 = 'fakeSiteDomain1';
    protected $siteDomain2 = 'fakeSiteDomain2';
    protected $configAliasForm;
    protected $thisconfigNodeForm;
    protected $formAlias;
    protected $formNode;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $siteAlias1 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteAliasInterface');
        Phake::when($siteAlias1)->getDomain()->thenReturn($this->siteDomain1);
        $siteAlias2 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteAliasInterface');
        Phake::when($siteAlias2)->getDomain()->thenReturn($this->siteDomain2);
        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($site)->getAliases()->thenReturn(array(
            'alias1' => $siteAlias1,
            'alias2' => $siteAlias2
        ));
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node)->getRoutePattern()->thenReturn('/{contentId}');

        $this->siteRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface');
        Phake::when($this->siteRepository)->findOneBySiteId(Phake::anyParameters())->thenReturn($site);

        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        Phake::when($this->nodeRepository)->findOnePublished(Phake::anyParameters())->thenReturn($node);

        $this->configAliasForm = Phake::mock('Symfony\Component\Form\FormConfigInterface');
        Phake::when($this->configAliasForm)->getOption(Phake::anyParameters())->thenReturn(array(
            'alias1' => $siteAlias1,
            'alias2' => $siteAlias2
        ));

        $this->configNodeForm = Phake::mock('Symfony\Component\Form\FormConfigInterface');
        Phake::when($this->configNodeForm)->getOption(Phake::anyParameters())->thenReturn(array());

        $this->form = Phake::mock('Symfony\Component\Form\FormInterface');
        $this->formAlias = Phake::mock('Symfony\Component\Form\FormInterface');
        $this->formNode = Phake::mock('Symfony\Component\Form\FormInterface');


        Phake::when($this->formAlias)->getConfig()->thenReturn($this->configAliasForm);
        Phake::when($this->formNode)->getConfig()->thenReturn($this->configNodeForm);

        Phake::when($this->form)->get('aliasId')->thenReturn($this->formAlias);
        Phake::when($this->form)->get('nodeId')->thenReturn($this->formNode);

        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($this->event)->getForm()->thenReturn($this->form);
        Phake::when($this->event)->getData()->thenReturn(array('siteId' => 'fakeSiteId'));

        $this->subscriber = new SiteSubscriber($this->siteRepository, $this->nodeRepository, array());
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->subscriber);
    }

    /**
     * @param string $eventName
     *
     * @dataProvider provideSubscribedEvent
     */
    public function testEventSubscribed($eventName)
    {
        $this->assertArrayHasKey($eventName, $this->subscriber->getSubscribedEvents());
    }

    /**
     * @return array
     */
    public function provideSubscribedEvent()
    {
        return array(
            array(
                FormEvents::POST_SET_DATA,
                FormEvents::PRE_SUBMIT,
            ),
        );
    }

    /**
     * Test preSubmit
     */
    public function testPreSubmit()
    {
        $this->subscriber->preSubmit($this->event);

        Phake::verify($this->form)->add('aliasId', 'choice', array(
            'label' => 'open_orchestra_backoffice.form.internal_link.site_alias',
            'attr' => array('class' => 'subform-to-refresh patch-submit-change'),
            'choices' => array(
                'alias1' => $this->siteDomain1 . '()',
                'alias2' => $this->siteDomain2 . '()',
            ),
            'required' => true,
        ));

        Phake::verify($this->form)->add('nodeId', 'oo_node_choice', array(
            'label' => 'open_orchestra_backoffice.form.internal_link.node',
            'siteId' => 'fakeSiteId',
            'attr' => array('class' => 'orchestra-tree-choice subform-to-refresh patch-submit-change'),
            'required' => true,
        ));
        Phake::verify($this->form)->add('wildcard', 'collection', array(
            'entry_type' => 'text',
            'label' => false,
            'attr' => array('class' => 'subform-to-refresh'),
            'data' => array('contentId' => ''),
            'entry_options' => array(
                'required' => true,
            ),
        ));

    }
}
