<?php

namespace PHPOrchestra\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use PHPOrchestra\CMSBundle\Form\Type\AreaType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AreaController
 */
class AreaController extends Controller
{
    /**
     * Render the node Area form
     *
     * @param string $type
     *
     * @return Response
     */
    public function formAction($type)
    {
        $form = $this->createForm(
            new AreaType(),
            null,
            array(
                'inDialog' => true,
                'subForm' => true,
                'beginJs' => array('pagegenerator/'.$type.'_area.js?'.time(), 'pagegenerator/dialogNode.js'),
            )
        );
        
        return $this->render(
            'PHPOrchestraCMSBundle:Form:form.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }
}
