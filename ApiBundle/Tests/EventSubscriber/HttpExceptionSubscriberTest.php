<?php

namespace OpenOrchestra\ApiBundle\Tests\EventSubscriber;

use Phake;
use OpenOrchestra\ApiBundle\EventSubscriber\HttpExceptionSubscriber;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;


/**
 * Class HttpExceptionSubscriberTest
 */
class HttpExceptionSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HttpExceptionSubscriber
     */
    protected $subscriber;

    protected $requestFormat = 'json';
    protected $response;
    protected $request;
    protected $kernel;
    protected $event;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->response = Phake::mock('Symfony\Component\HttpFoundation\Response');

        $this->kernel = Phake::mock('Symfony\Component\HttpKernel\Kernel');
        Phake::when($this->kernel)->handle(Phake::anyParameters())->thenReturn($this->response);

        $this->request = Phake::mock('Symfony\Component\HttpFoundation\Request');
        Phake::when($this->request)->duplicate(Phake::anyParameters())->thenReturn($this->request);
        Phake::when($this->request)->getRequestFormat(Phake::anyParameters())->thenReturn($this->requestFormat);

        $this->event = Phake::mock('Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent');
        Phake::when($this->event)->getRequest()->thenReturn($this->request);
        Phake::when($this->event)->getKernel()->thenReturn($this->kernel);

        $this->subscriber = new HttpExceptionSubscriber();
    }

    /**
     * Instance and event subscribed
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->subscriber);
        $this->assertSame(array(KernelEvents::EXCEPTION => array('onKernelException', 1000)), $this->subscriber->getSubscribedEvents());
    }

    /**
     * @param string $exceptionClass
     * @param int    $expectedTime
     *
     * @dataProvider provideExceptionClassAndExpectedTime
     */
    public function testWithHttpApiException($exceptionClass, $expectedTime)
    {
        $exception = Phake::mock($exceptionClass);

        Phake::when($this->event)->getException()->thenReturn($exception);

        $this->subscriber->onKernelException($this->event);

        Phake::verify($this->request, Phake::times($expectedTime))->duplicate(null, null, array(
            '_controller' => 'OpenOrchestra\ApiBundle\Controller\ExceptionController::showAction',
            'exception' => $exception,
            'format' => $this->requestFormat,
        ));
        Phake::verify($this->request, Phake::times($expectedTime))->setMethod('GET');
        Phake::verify($this->kernel, Phake::times($expectedTime))->handle($this->request, HttpKernelInterface::SUB_REQUEST, true);
        Phake::verify($this->event, Phake::times($expectedTime))->setResponse($this->response);
        Phake::verify($this->event, Phake::times($expectedTime))->stopPropagation();
    }

    /**
     * @return array
     */
    public function provideExceptionClassAndExpectedTime()
    {
        return array(
            array('OpenOrchestra\ApiBundle\Exceptions\HttpException\ApiException', 1),
            array('OpenOrchestra\ApiBundle\Exceptions\ApiException', 0),
        );
    }
}
