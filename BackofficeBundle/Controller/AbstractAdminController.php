<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

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
     * @var string
     */
    const TEMPLATE = 'OpenOrchestraBackofficeBundle::form.html.twig';

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
            $documentManager = $this->get('object_manager');

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
     * @param Response|null $response
     * @param string        $template
     *
     * @return Response
     */
    protected function renderAdminForm(
        FormInterface $form,
        array $params = array(),
        $response = null,
        $template = self::TEMPLATE
    ){

        if (is_null($response)) {
            $response = new Response('', Response::HTTP_OK, array('Content-type' => 'text/html; charset=utf-8'));
        }

        $params = array_merge($params, array('form' => $form->createView()));

        return $this->render($template, $params, $response);
    }

    /**
     * @param string $eventName
     * @param Event  $event
     */
    protected function dispatchEvent($eventName, $event)
    {
        $this->get('event_dispatcher')->dispatch($eventName, $event);
    }

    /**
     * @param string|\Symfony\Component\Form\FormTypeInterface $type
     * @param null                                             $data
     * @param array                                            $options
     *
     * @return \Symfony\Component\Form\Form
     */
    public function createForm($type, $data = null, array $options = array())
    {
        if ($data && !isset($options['disabled'])) {
            $options['disabled'] = !$this->get('open_orchestra_backoffice.authorize_edition.manager')->isEditable($data);
        }

        return parent::createForm($type, $data, $options);
    }
}
