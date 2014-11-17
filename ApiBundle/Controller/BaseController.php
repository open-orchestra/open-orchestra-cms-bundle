<?php

namespace PHPOrchestra\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
}