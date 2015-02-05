<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\Event;
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
     * @param mixed|null    $itemToPersist
     *
     * @return bool
     */
    protected function handleForm(FormInterface $form, $successMessage, $itemToPersist = null)
    {
        if ($form->isValid()) {
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');

            if ($itemToPersist) {
                $documentManager->persist($itemToPersist);
            }

            $documentManager->flush();

            $this->get('session')->getFlashBag()->add('success', $successMessage);

            return true;
        }

        return false;
    }

    /**
     * Render admin form and tag response with status 400 if form is badly completed
     *
     * @param FormInterface $form
     * @param array         $params additional view parameters
     *
     * @return Response
     */
    protected function renderAdminForm(FormInterface $form, array $params = array(), $response = null)
    {
        $statusCode = 200;
        if ($form->getErrors()->count() > 0) {
            $statusCode = 400;
        }

        if (is_null($response)) {
            $response = new Response('', $statusCode, array('Content-type' => 'text/html; charset=utf-8'));
        }

        $params = array_merge(
            $params,
            array('form' => $form->createView())
        );

        return $this->render(
            'PHPOrchestraBackofficeBundle:Editorial:template.html.twig',
            $params,
            $response
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
