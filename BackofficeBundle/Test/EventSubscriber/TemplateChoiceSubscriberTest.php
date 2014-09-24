<?php

namespace PHPOrchestra\BackofficeBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\BackofficeBundle\EventSubscriber\TemplateChoiceSubscriber;
use PHPOrchestra\BackofficeBundle\EventSubscriber\BlockTypeSubscriber;
use PHPOrchestra\ModelBundle\Model\TemplateInterface;
use Symfony\Component\Form\FormEvents;
use PHPOrchestra\ModelBundle\Document\Node;

/**
 * Class TemplateChoiceSubscriberTest
 */
class TemplateChoiceSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TemplateChoiceSubscriber
     */
    protected $subscriber;

    protected $event;
    protected $form;
    protected $templateRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($this->form)->add(Phake::anyParameters())->thenReturn($this->form);

        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($this->event)->getForm()->thenReturn($this->form);

        $this->templateRepository = Phake::mock('PHPOrchestra\ModelBundle\Repository\TemplateRepository');

        $this->subscriber = new TemplateChoiceSubscriber($this->templateRepository);
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
    }

    /**
     * @param array             $data
     * @param TemplateInterface $template
     *
     * @dataProvider getDataTemplate
     */
    public function testPreSubmit($data, $template)
    {
        $templateChoiceContainer = Phake::mock('PHPOrchestra\ModelBundle\Document\Node');

        Phake::when($templateChoiceContainer)->getData()->thenReturn($data);
        Phake::when($this->form)->getData()->thenReturn($templateChoiceContainer);

        Phake::when($this->event)->getData()->thenReturn($data);
        Phake::when($this->templateRepository)->findOneByTemplateId(Phake::anyParameters())->thenReturn($template);

        $this->subscriber->preSubmit($this->event);

        Phake::verify($templateChoiceContainer, Phake::times((null === $template)? 0: 1))->setAreas((null !== $template)? $template->getAreas() : '');
        Phake::verify($templateChoiceContainer, Phake::times((null === $template)? 0: 1))->setBlocks((null !== $template)? $template->getBlocks() : '');
    }

    /**
     * Templates provider
     *
     * @return array
     */
    public function getDataTemplate()
    {
        $areas = Phake::mock('Doctrine\Common\Collections\Collection');
        $blocks = Phake::mock('Doctrine\Common\Collections\Collection');

        $template = Phake::mock('PHPOrchestra\ModelBundle\Model\TemplateInterface');
        Phake::when($template)->getAreas()->thenReturn($areas);
        Phake::when($template)->getBlocks()->thenReturn($blocks);

        return array(
            array(array('templateId' => 1), $template),
            array(array('templateId' => 1), null),
        );
    }

    /**
     * @param array             $data
     * @param TemplateInterface $template
     *
     * @dataProvider getDataTemplate
     */
    public function testPreSubmitWithExistingNode($data, $template)
    {
        $templateChoiceContainer = Phake::mock('PHPOrchestra\ModelBundle\Document\Node');

        Phake::when($templateChoiceContainer)->getData()->thenReturn($data);
        Phake::when($templateChoiceContainer)->getId()->thenReturn('nodeId');
        Phake::when($this->form)->getData()->thenReturn($templateChoiceContainer);

        Phake::when($this->event)->getData()->thenReturn($data);
        Phake::when($this->templateRepository)->findOneByTemplateId(Phake::anyParameters())->thenReturn($template);

        $this->subscriber->preSubmit($this->event);

        Phake::verify($templateChoiceContainer, Phake::never())->setAreas(Phake::anyParameters());
        Phake::verify($templateChoiceContainer, Phake::never())->setBlocks(Phake::anyParameters());
    }
}
