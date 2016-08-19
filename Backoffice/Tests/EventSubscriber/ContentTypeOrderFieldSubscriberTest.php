<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\Backoffice\EventSubscriber\ContentTypeOrderFieldSubscriber;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\ContentTypeEvents;
use Phake;

/**
 * Class ContentTypeOrderFieldSubscriberTest
 */
class ContentTypeOrderFieldSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var ContentTypeOrderFieldSubscriber
     */
    protected $subscriber;
    protected $event;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->event = Phake::mock('OpenOrchestra\ModelInterface\Event\ContentTypeEvent');
        $this->subscriber = new ContentTypeOrderFieldSubscriber();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->subscriber);
    }

    /**
     * Test event subscribed
     */
    public function testEventSubscribed()
    {
        $this->assertArrayHasKey(ContentTypeEvents::CONTENT_TYPE_PRE_PERSIST, $this->subscriber->getSubscribedEvents());
    }

    /**
     * @param $fields
     * @param $expectedFields
     *
     * @dataProvider provideFields
     */
    public function testOrderFields($fields, $expectedFields)
    {
        $content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        Phake::when($this->event)->getContentType()->thenReturn($content);
        Phake::when($content)->getFields()->thenReturn($fields);

        $this->subscriber->orderFields($this->event);

        Phake::verify($content)->setFields($expectedFields);
    }

    /**
     * @return array
     */
    public function provideFields()
    {
        $field1 = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');
        Phake::when($field1)->getPosition()->thenReturn(0);

        $field2 = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');
        Phake::when($field2)->getPosition()->thenReturn(0);

        $field3 = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');
        Phake::when($field3)->getPosition()->thenReturn(2);

        $field4 = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');
        Phake::when($field4)->getPosition()->thenReturn(5);

        return array(
            array(new ArrayCollection(array($field1, $field2)), new ArrayCollection(array($field1, $field2))),
            array(new ArrayCollection(array($field2, $field1)), new ArrayCollection(array($field2, $field1))),
            array(new ArrayCollection(array($field2, $field3,  $field1)), new ArrayCollection(array($field1, $field2, $field3))),
            array(new ArrayCollection(array($field4, $field2, $field3,  $field1)), new ArrayCollection(array($field2, $field1, $field3, $field4))),
        );
    }
}
