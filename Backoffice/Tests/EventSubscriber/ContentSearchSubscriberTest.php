<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\EventSubscriber\ContentSearchSubscriber;
use Symfony\Component\Form\FormEvents;

/**
 * Class ChangeNodeContentSubscriberTest
 */
class ContentSearchSubscriberTest extends AbstractBaseTestCase
{
    protected $subscriber;
    protected $contentRepository;
    protected $contextManager;
    protected $form;
    protected $contentName1 = 'fakeContentName1';
    protected $contentId1 = 'fakeContentId1';
    protected $contentName2 = 'fakeContentName2';
    protected $contentId2 = 'fakeContentId2';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $contentType = 'fakeContentType';
        $choiceType = 'fakeChoiceType';
        $language = 'fakeLanguage';
        $siteId = 'fakeSiteId';

        $condition = 'fakeCondition';

        $content1 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content1)->getName()->thenReturn($this->contentName1);
        Phake::when($content1)->getContentId()->thenReturn($this->contentId1);
        $content2 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content2)->getName()->thenReturn($this->contentName2);
        Phake::when($content2)->getContentId()->thenReturn($this->contentId2);
        $this->contentRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface');

        Phake::when($this->contentRepository)->findByContentTypeAndCondition($language, $contentType, $choiceType, $condition, $siteId)->thenReturn(array(
                $content1,
                $content2
        ));
        $this->contextManager = Phake::mock('OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface');
        Phake::when($this->contextManager)->getCurrentSiteDefaultLanguage()->thenReturn($language);
        Phake::when($this->contextManager)->getCurrentSiteId()->thenReturn($siteId);

        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        $this->form = Phake::mock('Symfony\Component\Form\FormInterface');

        Phake::when($this->event)->getForm()->thenReturn($this->form);

        Phake::when($this->event)->getData()->thenReturn(array(
            'contentType' => $contentType,
            'choiceType' => $choiceType,
            'keywords' => $condition,
        ));

        $this->subscriber = new ContentSearchSubscriber(
            $this->contentRepository,
            $this->contextManager,
            true
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
            array(FormEvents::POST_SET_DATA),
        );
    }

    /**
     * Test preSubmit
     */
    public function testPreSubmit()
    {
        $config = Phake::mock('Symfony\Component\Form\FormConfigInterface');
        Phake::when($config)->getMethod()->thenReturn('PATCH');
        $parent = Phake::mock('Symfony\Component\Form\FormInterface');
        Phake::when($parent)->getConfig()->thenReturn($config);
        Phake::when($this->form)->getParent()->thenReturn($parent);

        $this->subscriber->preSubmit($this->event);

        Phake::verify($this->form)->add('refresh', 'button', array(
            'label' => 'open_orchestra_backoffice.form.content_search.refresh_content_list',
            'attr' => array('class' => 'patch-submit-click'),
        ));

        Phake::verify($this->form)->add('contentId', 'choice', array(
            'label' => false,
            'empty_value' => ' ',
            'required' => true,
            'choices' => array(
                $this->contentId1 => $this->contentName1,
                $this->contentId2 => $this->contentName2,
            ),
            'attr' => array('class' => 'subform-to-refresh'),
        ));
    }

    /**
     * Test postSubmit
     */
    public function testPostSetData()
    {
        $config = Phake::mock('Symfony\Component\Form\FormConfigInterface');
        Phake::when($config)->getMethod()->thenReturn('POST');
        $parent = Phake::mock('Symfony\Component\Form\FormInterface');
        Phake::when($parent)->getConfig()->thenReturn($config);
        Phake::when($this->form)->getRoot()->thenReturn($parent);

        $this->subscriber->postSetData($this->event);

        Phake::verify($this->form)->add('refresh', 'button', array(
            'label' => 'open_orchestra_backoffice.form.content_search.refresh_content_list',
            'attr' => array('class' => 'patch-submit-click'),
        ));

        Phake::verify($this->form)->add('contentId', 'choice', array(
            'label' => false,
            'empty_value' => ' ',
            'required' => true,
            'choices' => array(
                $this->contentId1 => $this->contentName1,
                $this->contentId2 => $this->contentName2,
            ),
            'attr' => array('class' => 'subform-to-refresh')
        ));
    }
}
