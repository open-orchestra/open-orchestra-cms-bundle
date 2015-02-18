<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TestController
 */
class TestController extends Controller
{

    public function newAction(Request $request)
    {
        $product = new Product();

        $form = $this->createForm(new ProductType(), $product, array(
            'action' => $this->generateUrl('target_route'),
            'method' => 'GET',
        ));

        $form = $this->createForm('orchestra_color_picker', $product);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $product = $form->getData();

            //enregistre dans la base
        }
        return $this->render('AcmeBundle:Default:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
