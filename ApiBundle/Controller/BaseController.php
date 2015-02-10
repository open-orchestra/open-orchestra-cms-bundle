<?php

namespace PHPOrchestra\ApiBundle\Controller;

use Doctrine\Common\Inflector\Inflector;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BaseController
 */
class BaseController extends Controller
{
    protected $violations;

    /**
     * @param mixed $mixed
     * @param array $validationGroups
     *
     * @return bool
     */
    protected function isValid($mixed, array $validationGroups = array())
    {
        $this->violations = $this->get('validator')->validate($mixed, $validationGroups);

        return 0 === count($this->getViolations());
    }

    /**
     * @return mixed
     */
    protected function getViolations()
    {
        return $this->violations;
    }

    /**
     * @param Request $request
     * @param string $id
     * @param string $type
     * @param string $event
     * @param string $eventClass
     *
     * @return Response
     */
    protected function reverseTransform(Request $request, $id, $type, $event, $eventClass)
    {
        $facadeName = Inflector::classify($type) . 'Facade';
        $typeName = Inflector::tableize($type);
        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            'PHPOrchestra\ApiBundle\Facade\\' . $facadeName,
            $request->get('_format', 'json')
        );

        $mixed = $this->get('php_orchestra_model.repository.' . $typeName)->find($id);
        $mixed = $this->get('php_orchestra_api.transformer_manager')->get($typeName)->reverseTransform($facade, $mixed);

        if ($this->isValid($mixed)) {
            $em = $this->get('doctrine.odm.mongodb.document_manager');
            $em->persist($mixed);
            $em->flush();

            $this->dispatchEvent($event, new $eventClass($mixed));

            return new Response('', 200);
        }

        return new response(
            $this->get('jms_serializer')->serialize($this->getViolations(), $request->get('_format', 'json')),
            400
        );
    }

    /**
     * @param string $eventName
     * @param Event  $event
     */
    protected function dispatchEvent($eventName, $event)
    {
        $this->get('event_dispatcher')->dispatch($eventName, $event);
    }
}
