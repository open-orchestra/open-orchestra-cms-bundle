<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class InternalLinkController
 */
class InternalLinkController extends AbstractAdminController
{
    /**
     * @Config\Route("/internal_link/form", name="open_orchestra_backoffice_internal_link_form")
     *
     * @return Response
     */
    public function formAction(Request $request)
    {
        $form = $this->createForm(
            'oo_internal_link',
            null,
            array(
                'action' => $this->generateUrl('open_orchestra_backoffice_internal_link_form'),
            )
        );
        $form->handleRequest($request);

        return $this->renderAdminForm($form);
    }
}
