<?php

namespace OpenOrchestra\ApiBundle\Tests\EventSubscriber;

use OpenOrchestra\ApiBundle\EventSubscriber\SerializerSubscriber;
use Phake;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Test SerializerSubscriberTest
 */
class SerializerSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SerializerSubscriber
     */
    protected $subscriber;

    protected $event;
    protected $request;
    protected $resolver;
    protected $serializer;
    protected $annotationReader;
    protected $controllerResult;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->resolver = Phake::mock('Symfony\Component\HttpKernel\Controller\ControllerResolverInterface');
        $this->serializer = Phake::mock('JMS\Serializer\SerializerInterface');
        $this->annotationReader = Phake::mock('Doctrine\Common\Annotations\AnnotationReader');

        $this->request = Phake::mock('Symfony\Component\HttpFoundation\Request');
        Phake::when($this->request)->get('_route')->thenReturn('open_orchestra_api');

        $this->controllerResult = array();

        $kernel = Phake::mock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $this->event = new GetResponseForControllerResultEvent(
            $kernel,
            $this->request,
            HttpKernelInterface::MASTER_REQUEST,
            $this->controllerResult
        );

        $this->subscriber = new SerializerSubscriber($this->serializer, $this->annotationReader, $this->resolver);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->subscriber);
    }

    /**
     * Test event subscribed and method callable
     */
    public function testEventSubscribedAndMethodCallable()
    {
        $this->assertArrayHasKey(KernelEvents::VIEW, $this->subscriber->getSubscribedEvents());
        $this->assertTrue(method_exists($this->subscriber, 'onKernelViewSerialize'));
    }

    /**
     * @param string $format
     * @param string $responseContentType
     *
     * @dataProvider provideFormatAndResponseType
     */
    public function testOnKernelViewSerialize($format, $responseContentType)
    {
        Phake::when($this->resolver)->getController(Phake::anyParameters())->thenReturn(array('\DateTime', 'add'));
        Phake::when($this->annotationReader)->getMethodAnnotation(Phake::anyParameters())->thenReturn(true);
        Phake::when($this->request)->get('_format', 'json')->thenReturn($format);

        $this->subscriber->onKernelViewSerialize($this->event);

        /** @var Response $response */
        $response = $this->event->getResponse();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals(200, $response->getStatusCode());
        Phake::verify($this->serializer)->serialize($this->controllerResult, $format);
        $this->assertSame($responseContentType, $response->headers->get('content-type'));
    }

    /**
     * @return array
     */
    public function provideFormatAndResponseType()
    {
        return array(
            array('json', 'application/json'),
            array('xml', 'text/xml'),
            array('yml', 'application/yaml'),
        );
    }
}
