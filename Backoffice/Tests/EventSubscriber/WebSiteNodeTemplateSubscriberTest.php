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
    protected $templateRepository;
    /** @var WebSiteNodeTemplateSubscriber  */
    protected $subscriber;
    protected $data;
    protected $event;
    protected $form;
    protected $templateChoices = array();

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

        $this->templateRepository = Phake::mock('OpenOrchestra\ModelBundle\Repository\TemplateRepository');
        Phake::when($this->templateRepository)->findByDeleted(false)->thenReturn($this->templateChoices);

        $this->subscriber = new WebSiteNodeTemplateSubscriber($this->templateRepository);
    }

    /**
     * @param string $siteId
     * @param int    $countAddForm
     *
     * @dataProvider provideSiteIdAndCountAddForm
     */
    public function testOnPreSetData($siteId, $countAddForm)
    {
        Phake::when($this->data)->getSiteId()->thenReturn($siteId);
        $this->subscriber->onPreSetData($this->event);
        Phake::verify($this->form, Phake::times($countAddForm))->add('templateId', 'choice', array(
            'choices' => $this->templateChoices,
            'required' => true,
            'mapped' => false,
            'label' => 'open_orchestra_backoffice.form.website.template_node_root.label',
            'attr'  => array(
                'help_text' => 'open_orchestra_backoffice.form.website.template_node_root.helper',
            )
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
