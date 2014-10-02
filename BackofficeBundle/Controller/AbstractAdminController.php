<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;

/**
 * Class AbstractController
 */
abstract class AbstractAdminController extends Controller
{
    /**
     * Do some stuff if admin form is valid
     * 
     * @param FormInterface $form
     * @param string        $successMessage
     * @param mixed         $persistingItem
     */
    protected function handleForm(FormInterface $form, $successMessage, $itemTopersist = null)
    {
        if ($form->isValid()) {
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');

            if ($itemTopersist) {
                $documentManager->persist($itemTopersist);
            }

            $documentManager->flush();

            $this->get('session')->getFlashBag()->add('success', $successMessage);
        }
    }

    /**
     * Render admin form and tag response with status 400 if form is badly completed
     * 
     * @param FormInterface $form
     */
    protected function renderAdminForm(FormInterface $form)
    {
        if ($form->getErrors()->count() > 0) {
            $statusCode = 400;
        } else {
            $statusCode = 200;
        };

        $response = new Response('', $statusCode, array('Content-type' => 'text/html; charset=utf-8'));

        return $this->render(
            'PHPOrchestraBackofficeBundle:Editorial:template.html.twig',
            array(
                'form' => $form->createView()
            ),
            $response
        );
    }
}
