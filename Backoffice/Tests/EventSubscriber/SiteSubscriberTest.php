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
            $siteAlias1,
            $siteAlias2
        ));
        $this->siteRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface');
        Phake::when($this->siteRepository)->findOneBySiteId(Phake::anyParameters())->thenReturn($site);

        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        $this->form = Phake::mock('Symfony\Component\Form\FormInterface');

        Phake::when($this->event)->getForm()->thenReturn($this->form);

        Phake::when($this->event)->getData()->thenReturn(array('siteId' => 'fakeSiteId'));

        $this->subscriber = new SiteSubscriber($this->siteRepository);
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
            array(FormEvents::PRE_SUBMIT),
        );
    }

    /**
     * Test preSubmit
     */
    public function testPreSubmit()
    {
        $this->subscriber->preSubmit($this->event);

        Phake::verify($this->form)->add('siteAlias', 'choice', array(
            'label' => 'open_orchestra_backoffice.form.internal_link.site_alias',
            'attr' => array(
                'class' => 'to-tinyMce',
                'data-key' => 'site-alias'
            ),
            'required' => false,
            'choices' => array(
                        $this->siteDomain1 => $this->siteDomain1,
                        $this->siteDomain2 => $this->siteDomain2,
                   ),
            'required' => false,
        ));
    }
}
