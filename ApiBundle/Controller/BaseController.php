<?php

namespace PHPOrchestra\ApiBundle\Controller;

use Doctrine\Common\Inflector\Inflector;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
    public function isValid($mixed, array $validationGroups = array())
    {
        $this->violations = $this->get('validator')->validate($mixed, $validationGroups);

        return 0 === count($this->getViolations());
    }

    /**
     * @return mixed
     */
    public function getViolations()
    {
        return $this->violations;
    }

    /**
     * @param Request $request
     * @param string $id
     * @param string $type
     *
     * @return Response
     */
    protected function reverseTransform(Request $request, $id, $type)
    {
        $facadeName = Inflector::classify($type) . 'Facade';
        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            'PHPOrchestra\ApiBundle\Facade\\' . $facadeName,
            $request->get('_format', 'json')
        );

        $mixed = $this->get('php_orchestra_model.repository.' . $type)->find($id);
        $mixed = $this->get('php_orchestra_api.transformer_manager')->get($type)->reverseTransform($facade, $mixed);

        if ($this->isValid($mixed)) {
            $em = $this->get('doctrine.odm.mongodb.document_manager');
            $em->persist($mixed);
            $em->flush();

            return new Response('', 200);
        }

        return new response(
            $this->get('jms_serializer')->serialize($this->getViolations(), $request->get('_format', 'json')),
            400
        );
    }
}
