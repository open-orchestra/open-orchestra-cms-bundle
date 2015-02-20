<?php

namespace PHPOrchestra\BackofficeBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\BackofficeBundle\EventSubscriber\WebSiteSubscriber;

/**
 * Class WebSiteSubscriberTest
 */
class WebSiteSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var WebSiteSubscriber
     */
    protected $subscriber;

    protected $event;
    protected $form;
    protected $data;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = Phake::mock('Symfony\Component\Form\Form');
        $this->data = Phake::mock('PHPOrchestra\ModelInterface\Model\SiteInterface');
        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($this->event)->getForm()->thenReturn($this->form);
        Phake::when($this->event)->getData()->thenReturn($this->data);

        $this->subscriber = new WebSiteSubscriber();
    }

    /**
     * @param string $siteId
     * @param array $options
     *
     * @dataProvider generateOptions
     */
    public function testOnPreSetData($siteId, $options)
    {
        Phake::when($this->data)->getSiteId()->thenReturn($siteId);

        $this->subscriber->onPreSetData($this->event);

        Phake::verify($this->form)->add('siteId', 'text', $options);
    }

    /**
     * @return array
     */
    public function generateOptions()
    {
        return array(
            array(null, array(
                'label' => 'php_orchestra_backoffice.form.website.site_id',
                'attr' => array('class' => 'generate-id-dest')
            )),
            array('siteId', array(
                'label' => 'php_orchestra_backoffice.form.website.site_id',
                'attr' => array('class' => 'generate-id-dest'),
                'disabled' => true
            ))
        );
    }
}
