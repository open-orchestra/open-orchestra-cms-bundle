<?php

namespace OpenOrchestra\WorkflowAdminBundle\Controller\Admin;

use OpenOrchestra\BackofficeBundle\Controller\AbstractAdminController;
use OpenOrchestra\Fonction\Event\FonctionEvent;
use OpenOrchestra\Fonction\FonctionEvents;
use OpenOrchestra\Fonction\Model\FonctionInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FonctionController
 */
class FonctionController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param string  $fonctionId
     *
     * @Config\Route("/fonction/form/{fonctionId}", name="open_orchestra_backoffice_fonction_form")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_FONCTION')")
     *
     * @return Response
     */
    public function formAction(Request $request, $fonctionId)
    {
        $fonctionRepository = $this->container->get('open_orchestra_workflow.repository.fonction');
        $fonction = $fonctionRepository->find($fonctionId);

        $url = $this->generateUrl('open_orchestra_backoffice_fonction_form', array('fonctionId' => $fonctionId));

        return $this->generateForm($request, $fonction, $url, FonctionEvents::FONCTION_UPDATE);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/fonction/new", name="open_orchestra_backoffice_fonction_new")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_FONCTION')")
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $fonctionClass = $this->container->getParameter('open_orchestra_workflow.document.fonction.class');
        /** @var FonctionInterface $fonction */
        $fonction = new $fonctionClass();

        $url = $this->generateUrl('open_orchestra_backoffice_fonction_new');

        return $this->generateForm($request, $fonction, $url, FonctionEvents::FONCTION_CREATE);
    }

    /**
     * @param Request           $request
     * @param FonctionInterface $fonction
     * @param string            $url
     * @param string            $fonctionEvents
     *
     * @return Response
     */
    protected function generateForm(Request $request, FonctionInterface $fonction, $url, $fonctionEvents)
    {
        $form = $this->createForm('fonction', $fonction, array('action' => $url));
        $form->handleRequest($request);
        $this->handleForm(
            $form,
            $this->get('translator')->trans('open_orchestra_workflow.form.fonction.success'),
            $fonction
        );

        $this->dispatchEvent($fonctionEvents, new FonctionEvent($fonction));

        return $this->renderAdminForm($form);
    }
}
