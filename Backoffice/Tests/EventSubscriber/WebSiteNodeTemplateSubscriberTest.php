<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\Backoffice\EventSubscriber\WebSiteNodeTemplateSubscriber;
use Phake;
use Symfony\Component\Form\FormEvents;

/**
 * Class WebSiteNodeTemplateSubscriberTest
 */
class WebSiteNodeTemplateSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /** @var WebSiteNodeTemplateSubscriber  */
    protected $subscriber;
    protected $data;
    protected $event;
    protected $form;
    protected $templateSet = 'fakeTemplateSet';
    protected $templateSetData = array(
        'label' => 'fakeTemplateSet',
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
        $this->form = Phake::mock('Symfony\Component\Form\Form');
        $this->data = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($this->event)->getForm()->thenReturn($this->form);
        Phake::when($this->event)->getData()->thenReturn($this->data);
        Phake::when($this->form)->get(Phake::anyParameters())->thenReturn($this->form);
        $this->templateManager = Phake::mock('OpenOrchestra\Backoffice\Manager\TemplateManager');
        Phake::when($this->templateManager)->getTemplateSetParameters()->thenReturn(array(
            $this->templateSet => $this->templateSetData
        ));

        $this->subscriber = new WebSiteNodeTemplateSubscriber($this->templateManager);
    }

    /**
     * @param string $siteId
     * @param int    $countAddForm
     *
     * @dataProvider provideSiteIdAndCountAddForm
     */
    public function testPreSetData($siteId, $countAddForm)
    {
        $templateSetChoices = array(
            'fakeTemplateSet' => 'fakeTemplateSet'
        );
        $templateChoices = array(
            'fakeTemplateSet' => array(
                'default' => 'default',
                'full_page' => 'full_page',
            )
        );

        Phake::when($this->data)->getSiteId()->thenReturn($siteId);

        $this->subscriber->onPreSetData($this->event);
        Phake::verify($this->form, Phake::times($countAddForm))->add('templateSet', 'choice', array(
            'label' => 'open_orchestra_backoffice.form.website.template_set',
            'choices' => $templateSetChoices,
            'attr' => array('class' => 'select-grouping-master'),
            'required' => true,
        ));
        Phake::verify($this->form, Phake::times($countAddForm))->add('templateRoot', 'choice', array(
            'label' => 'open_orchestra_backoffice.form.website.template_node_root.label',
            'choices' => $templateChoices,
            'attr'  => array(
                'help_text' => 'open_orchestra_backoffice.form.website.template_node_root.helper',
                'class' => 'select-grouping-slave'
            ),
            'required' => true,
        ));
    }

    /**
     * @return array
     */
    public function provideSiteIdAndCountAddForm()
    {
        return array(
            "with site id in data" => array('fakeSiteId', 0),
            "no site id in data" => array(null, 1)
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
